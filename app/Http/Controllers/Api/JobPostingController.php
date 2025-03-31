<?php

namespace App\Http\Controllers\Api;

use App\Models\JobPosting;
use App\Enums\EmploymentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobPostingController extends BaseController
{
    /**
     * Display a listing of the job postings.
     */
    public function index(Request $request)
    {
        $query = JobPosting::query();

        // Apply filters
        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->has('employment_type')) {
            $query->where('employment_type', $request->employment_type);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Apply search
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Apply sorting
        $sortField = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Paginate results
        $perPage = $request->get('per_page', 15);
        $jobPostings = $query->paginate($perPage);

        return $this->sendResponse($jobPostings, 'Job postings retrieved successfully.');
    }

    /**
     * Store a newly created job posting.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'required|array',
            'responsibilities' => 'required|array',
            'employment_type' => 'required|in:' . implode(',', array_column(EmploymentType::cases(), 'value')),
            'department_id' => 'required|exists:departments,id',
            'location' => 'required|string|max:255',
            'salary_range_min' => 'required|numeric|min:0',
            'salary_range_max' => 'required|numeric|min:0|gte:salary_range_min',
            'experience_years' => 'required|numeric|min:0',
            'education_level' => 'required|string|max:255',
            'skills_required' => 'required|array',
            'benefits' => 'required|array',
            'deadline' => 'required|date|after:today',
            'is_active' => 'boolean',
            'status' => 'required|string|max:50',
            'notes' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $jobPosting = JobPosting::create($request->all());

        return $this->sendResponse($jobPosting, 'Job posting created successfully.', 201);
    }

    /**
     * Display the specified job posting.
     */
    public function show(JobPosting $jobPosting)
    {
        return $this->sendResponse($jobPosting, 'Job posting retrieved successfully.');
    }

    /**
     * Update the specified job posting.
     */
    public function update(Request $request, JobPosting $jobPosting)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'requirements' => 'sometimes|required|array',
            'responsibilities' => 'sometimes|required|array',
            'employment_type' => 'sometimes|required|in:' . implode(',', array_column(EmploymentType::cases(), 'value')),
            'department_id' => 'sometimes|required|exists:departments,id',
            'location' => 'sometimes|required|string|max:255',
            'salary_range_min' => 'sometimes|required|numeric|min:0',
            'salary_range_max' => 'sometimes|required|numeric|min:0|gte:salary_range_min',
            'experience_years' => 'sometimes|required|numeric|min:0',
            'education_level' => 'sometimes|required|string|max:255',
            'skills_required' => 'sometimes|required|array',
            'benefits' => 'sometimes|required|array',
            'deadline' => 'sometimes|required|date|after:today',
            'is_active' => 'boolean',
            'status' => 'sometimes|required|string|max:50',
            'notes' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $jobPosting->update($request->all());

        return $this->sendResponse($jobPosting, 'Job posting updated successfully.');
    }

    /**
     * Remove the specified job posting.
     */
    public function destroy(JobPosting $jobPosting)
    {
        $jobPosting->delete();
        return $this->sendResponse([], 'Job posting deleted successfully.');
    }

    /**
     * Update job posting status.
     */
    public function updateStatus(Request $request, JobPosting $jobPosting)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $jobPosting->update(['status' => $request->status]);
        return $this->sendResponse($jobPosting, 'Job posting status updated successfully.');
    }

    /**
     * Toggle job posting active status.
     */
    public function toggleActive(JobPosting $jobPosting)
    {
        $jobPosting->update(['is_active' => !$jobPosting->is_active]);
        return $this->sendResponse($jobPosting, 'Job posting active status updated successfully.');
    }
} 