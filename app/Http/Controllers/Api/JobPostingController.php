<?php

namespace App\Http\Controllers\Api;

use App\Enums\JobPostingStatus;
use App\Enums\JobType;
use App\Enums\RemoteType;
use App\Models\JobPosting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class JobPostingController extends BaseController
{
    /**
     * Display a listing of job postings.
     */
    public function index(Request $request)
    {
        $query = QueryBuilder::for(JobPosting::class)
            ->allowedFilters([
                AllowedFilter::exact('status'),
                AllowedFilter::exact('job_type'),
                AllowedFilter::exact('remote_type'),
                AllowedFilter::exact('department_id'),
                AllowedFilter::exact('position_id'),
                AllowedFilter::partial('title'),
                AllowedFilter::partial('location'),
                AllowedFilter::scope('salary_range'),
                AllowedFilter::scope('experience_range'),
            ])
            ->allowedSorts([
                AllowedSort::field('title'),
                AllowedSort::field('created_at'),
                AllowedSort::field('published_at'),
                AllowedSort::field('closing_date'),
                AllowedSort::field('salary_min'),
                AllowedSort::field('salary_max'),
                AllowedSort::field('experience_years'),
            ])
            ->with(['department', 'position'])
            ->where('company_id', $request->user()->company_id)
            ->latest();

        $result = $query->paginate($request->get('per_page', 15));

        return $this->sendResponse($result, 'Job postings retrieved successfully');
    }

    /**
     * Store a newly created job posting.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'required|array',
            'responsibilities' => 'required|array',
            'qualifications' => 'required|array',
            'experience_years' => 'nullable|numeric|min:0',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0',
            'salary_type' => 'nullable|string|in:hourly,monthly,yearly',
            'job_type' => 'required|string|in:' . implode(',', array_column(JobType::cases(), 'value')),
            'location' => 'nullable|string|max:255',
            'remote_type' => 'nullable|string|in:' . implode(',', array_column(RemoteType::cases(), 'value')),
            'closing_date' => 'nullable|date|after:today',
            'settings' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $jobPosting = JobPosting::create([
            'company_id' => $request->user()->company_id,
            'department_id' => $request->department_id,
            'position_id' => $request->position_id,
            'title' => $request->title,
            'description' => $request->description,
            'requirements' => $request->requirements,
            'responsibilities' => $request->responsibilities,
            'qualifications' => $request->qualifications,
            'experience_years' => $request->experience_years,
            'salary_min' => $request->salary_min,
            'salary_max' => $request->salary_max,
            'salary_type' => $request->salary_type,
            'job_type' => $request->job_type,
            'location' => $request->location,
            'remote_type' => $request->remote_type,
            'status' => JobPostingStatus::DRAFT,
            'closing_date' => $request->closing_date,
            'settings' => $request->settings,
        ]);

        return $this->sendResponse($jobPosting, 'Job posting created successfully');
    }

    /**
     * Display the specified job posting.
     */
    public function show(JobPosting $jobPosting)
    {
        if ($jobPosting->company_id !== request()->user()->company_id) {
            return $this->sendForbidden('You do not have permission to view this job posting');
        }

        $jobPosting->load(['department', 'position', 'candidates']);

        return $this->sendResponse($jobPosting, 'Job posting retrieved successfully');
    }

    /**
     * Update the specified job posting.
     */
    public function update(Request $request, JobPosting $jobPosting)
    {
        if ($jobPosting->company_id !== $request->user()->company_id) {
            return $this->sendForbidden('You do not have permission to update this job posting');
        }

        $validator = Validator::make($request->all(), [
            'department_id' => 'sometimes|exists:departments,id',
            'position_id' => 'sometimes|exists:positions,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'requirements' => 'sometimes|array',
            'responsibilities' => 'sometimes|array',
            'qualifications' => 'sometimes|array',
            'experience_years' => 'nullable|numeric|min:0',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0',
            'salary_type' => 'nullable|string|in:hourly,monthly,yearly',
            'job_type' => 'sometimes|string|in:' . implode(',', array_column(JobType::cases(), 'value')),
            'location' => 'nullable|string|max:255',
            'remote_type' => 'nullable|string|in:' . implode(',', array_column(RemoteType::cases(), 'value')),
            'closing_date' => 'nullable|date|after:today',
            'settings' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $jobPosting->update($request->all());

        return $this->sendResponse($jobPosting, 'Job posting updated successfully');
    }

    /**
     * Remove the specified job posting.
     */
    public function destroy(JobPosting $jobPosting)
    {
        if ($jobPosting->company_id !== request()->user()->company_id) {
            return $this->sendForbidden('You do not have permission to delete this job posting');
        }

        $jobPosting->delete();

        return $this->sendResponse([], 'Job posting deleted successfully');
    }

    /**
     * Update the status of the specified job posting.
     */
    public function updateStatus(Request $request, JobPosting $jobPosting)
    {
        if ($jobPosting->company_id !== $request->user()->company_id) {
            return $this->sendForbidden('You do not have permission to update this job posting');
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:' . implode(',', array_column(JobPostingStatus::cases(), 'value')),
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $jobPosting->update([
            'status' => $request->status,
            'published_at' => $request->status === JobPostingStatus::PUBLISHED ? now() : null,
        ]);

        return $this->sendResponse($jobPosting, 'Job posting status updated successfully');
    }
} 