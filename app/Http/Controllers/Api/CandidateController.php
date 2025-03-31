<?php

namespace App\Http\Controllers\Api;

use App\Models\Candidate;
use App\Enums\CandidateStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class CandidateController extends BaseController
{
    /**
     * Display a listing of the candidates.
     */
    public function index(Request $request)
    {
        $query = Candidate::query();

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('job_posting_id')) {
            $query->where('job_posting_id', $request->job_posting_id);
        }

        // Apply search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $sortField = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Paginate results
        $perPage = $request->get('per_page', 15);
        $candidates = $query->paginate($perPage);

        return $this->sendResponse($candidates, 'Candidates retrieved successfully.');
    }

    /**
     * Store a newly created candidate.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:candidates,email',
            'phone' => 'nullable|string|max:20',
            'job_posting_id' => 'required|exists:job_postings,id',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:2048',
            'cover_letter' => 'nullable|string',
            'experience_years' => 'nullable|numeric|min:0',
            'current_salary' => 'nullable|numeric|min:0',
            'expected_salary' => 'nullable|numeric|min:0',
            'notice_period' => 'nullable|integer|min:0',
            'status' => 'required|in:' . implode(',', array_column(CandidateStatus::cases(), 'value')),
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $candidate = Candidate::create($request->all());

        // Handle file upload
        if ($request->hasFile('resume')) {
            $path = $request->file('resume')->store('candidates/resumes', 'public');
            $candidate->resume_path = $path;
            $candidate->save();
        }

        return $this->sendResponse($candidate, 'Candidate created successfully.', 201);
    }

    /**
     * Display the specified candidate.
     */
    public function show(Candidate $candidate)
    {
        return $this->sendResponse($candidate, 'Candidate retrieved successfully.');
    }

    /**
     * Update the specified candidate.
     */
    public function update(Request $request, Candidate $candidate)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:candidates,email,' . $candidate->id,
            'phone' => 'nullable|string|max:20',
            'job_posting_id' => 'sometimes|required|exists:job_postings,id',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'cover_letter' => 'nullable|string',
            'experience_years' => 'nullable|numeric|min:0',
            'current_salary' => 'nullable|numeric|min:0',
            'expected_salary' => 'nullable|numeric|min:0',
            'notice_period' => 'nullable|integer|min:0',
            'status' => 'sometimes|required|in:' . implode(',', array_column(CandidateStatus::cases(), 'value')),
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $candidate->update($request->all());

        // Handle file upload
        if ($request->hasFile('resume')) {
            // Delete old resume if exists
            if ($candidate->resume_path) {
                Storage::disk('public')->delete($candidate->resume_path);
            }
            
            $path = $request->file('resume')->store('candidates/resumes', 'public');
            $candidate->resume_path = $path;
            $candidate->save();
        }

        return $this->sendResponse($candidate, 'Candidate updated successfully.');
    }

    /**
     * Remove the specified candidate.
     */
    public function destroy(Candidate $candidate)
    {
        // Delete resume file if exists
        if ($candidate->resume_path) {
            Storage::disk('public')->delete($candidate->resume_path);
        }

        $candidate->delete();
        return $this->sendResponse([], 'Candidate deleted successfully.');
    }

    /**
     * Update candidate status.
     */
    public function updateStatus(Request $request, Candidate $candidate)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:' . implode(',', array_column(CandidateStatus::cases(), 'value')),
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $candidate->update(['status' => $request->status]);
        return $this->sendResponse($candidate, 'Candidate status updated successfully.');
    }
} 