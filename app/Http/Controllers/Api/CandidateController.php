<?php

namespace App\Http\Controllers\Api;

use App\Enums\CandidateStatus;
use App\Models\Candidate;
use App\Models\JobPosting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class CandidateController extends BaseController
{
    /**
     * Display a listing of candidates.
     */
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Candidate::class)
            ->allowedFilters([
                AllowedFilter::exact('status'),
                AllowedFilter::exact('job_posting_id'),
                AllowedFilter::partial('first_name'),
                AllowedFilter::partial('last_name'),
                AllowedFilter::partial('email'),
                AllowedFilter::scope('experience_range'),
                AllowedFilter::scope('salary_range'),
            ])
            ->allowedSorts([
                AllowedSort::field('first_name'),
                AllowedSort::field('last_name'),
                AllowedSort::field('email'),
                AllowedSort::field('created_at'),
                AllowedSort::field('experience_years'),
                AllowedSort::field('current_salary'),
                AllowedSort::field('expected_salary'),
            ])
            ->with(['jobPosting'])
            ->where('company_id', $request->user()->company_id)
            ->latest();

        $result = $query->paginate($request->get('per_page', 15));

        return $this->sendResponse($result, 'Candidates retrieved successfully');
    }

    /**
     * Store a newly created candidate.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'job_posting_id' => 'required|exists:job_postings,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'cover_letter' => 'nullable|string',
            'experience_years' => 'nullable|numeric|min:0',
            'current_salary' => 'nullable|numeric|min:0',
            'expected_salary' => 'nullable|numeric|min:0',
            'notice_period' => 'nullable|integer|min:0',
            'notes' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        // Check if job posting belongs to company
        $jobPosting = JobPosting::findOrFail($request->job_posting_id);
        if ($jobPosting->company_id !== $request->user()->company_id) {
            return $this->sendForbidden('You do not have permission to add candidates to this job posting');
        }

        // Handle resume upload
        $resumePath = $request->file('resume')->store('resumes', 'public');

        $candidate = Candidate::create([
            'company_id' => $request->user()->company_id,
            'job_posting_id' => $request->job_posting_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'resume_path' => $resumePath,
            'cover_letter' => $request->cover_letter,
            'experience_years' => $request->experience_years,
            'current_salary' => $request->current_salary,
            'expected_salary' => $request->expected_salary,
            'notice_period' => $request->notice_period,
            'status' => CandidateStatus::APPLIED,
            'notes' => $request->notes,
        ]);

        return $this->sendResponse($candidate, 'Candidate created successfully');
    }

    /**
     * Display the specified candidate.
     */
    public function show(Candidate $candidate)
    {
        if ($candidate->company_id !== request()->user()->company_id) {
            return $this->sendForbidden('You do not have permission to view this candidate');
        }

        $candidate->load(['jobPosting', 'interviews']);

        return $this->sendResponse($candidate, 'Candidate retrieved successfully');
    }

    /**
     * Update the specified candidate.
     */
    public function update(Request $request, Candidate $candidate)
    {
        if ($candidate->company_id !== $request->user()->company_id) {
            return $this->sendForbidden('You do not have permission to update this candidate');
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255',
            'phone' => 'nullable|string|max:20',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'cover_letter' => 'nullable|string',
            'experience_years' => 'nullable|numeric|min:0',
            'current_salary' => 'nullable|numeric|min:0',
            'expected_salary' => 'nullable|numeric|min:0',
            'notice_period' => 'nullable|integer|min:0',
            'notes' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $data = $request->except('resume');

        // Handle resume upload if provided
        if ($request->hasFile('resume')) {
            // Delete old resume
            if ($candidate->resume_path) {
                Storage::disk('public')->delete($candidate->resume_path);
            }
            $data['resume_path'] = $request->file('resume')->store('resumes', 'public');
        }

        $candidate->update($data);

        return $this->sendResponse($candidate, 'Candidate updated successfully');
    }

    /**
     * Remove the specified candidate.
     */
    public function destroy(Candidate $candidate)
    {
        if ($candidate->company_id !== request()->user()->company_id) {
            return $this->sendForbidden('You do not have permission to delete this candidate');
        }

        // Delete resume file
        if ($candidate->resume_path) {
            Storage::disk('public')->delete($candidate->resume_path);
        }

        $candidate->delete();

        return $this->sendResponse([], 'Candidate deleted successfully');
    }

    /**
     * Update the status of the specified candidate.
     */
    public function updateStatus(Request $request, Candidate $candidate)
    {
        if ($candidate->company_id !== $request->user()->company_id) {
            return $this->sendForbidden('You do not have permission to update this candidate');
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:' . implode(',', array_column(CandidateStatus::cases(), 'value')),
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $candidate->update(['status' => $request->status]);

        return $this->sendResponse($candidate, 'Candidate status updated successfully');
    }
} 