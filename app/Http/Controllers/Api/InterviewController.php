<?php

namespace App\Http\Controllers\Api;

use App\Enums\InterviewStatus;
use App\Enums\InterviewType;
use App\Models\Candidate;
use App\Models\Interview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class InterviewController extends BaseController
{
    /**
     * Display a listing of interviews.
     */
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Interview::class)
            ->allowedFilters([
                AllowedFilter::exact('type'),
                AllowedFilter::exact('status'),
                AllowedFilter::exact('candidate_id'),
                AllowedFilter::exact('interviewer_id'),
                AllowedFilter::scope('date_range'),
            ])
            ->allowedSorts([
                AllowedSort::field('scheduled_at'),
                AllowedSort::field('created_at'),
                AllowedSort::field('duration_minutes'),
                AllowedSort::field('rating'),
            ])
            ->with(['candidate', 'interviewer'])
            ->where('company_id', $request->user()->company_id)
            ->latest();

        $result = $query->paginate($request->get('per_page', 15));

        return $this->sendResponse($result, 'Interviews retrieved successfully');
    }

    /**
     * Store a newly created interview.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'candidate_id' => 'required|exists:candidates,id',
            'interviewer_id' => 'required|exists:users,id',
            'type' => 'required|string|in:' . implode(',', array_column(InterviewType::cases(), 'value')),
            'scheduled_at' => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:15',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        // Check if candidate belongs to company
        $candidate = Candidate::findOrFail($request->candidate_id);
        if ($candidate->company_id !== $request->user()->company_id) {
            return $this->sendForbidden('You do not have permission to schedule interviews for this candidate');
        }

        $interview = Interview::create([
            'company_id' => $request->user()->company_id,
            'candidate_id' => $request->candidate_id,
            'interviewer_id' => $request->interviewer_id,
            'type' => $request->type,
            'status' => InterviewStatus::SCHEDULED,
            'scheduled_at' => $request->scheduled_at,
            'duration_minutes' => $request->duration_minutes,
            'location' => $request->location,
            'notes' => $request->notes,
        ]);

        return $this->sendResponse($interview, 'Interview scheduled successfully');
    }

    /**
     * Display the specified interview.
     */
    public function show(Interview $interview)
    {
        if ($interview->company_id !== request()->user()->company_id) {
            return $this->sendForbidden('You do not have permission to view this interview');
        }

        $interview->load(['candidate', 'interviewer']);

        return $this->sendResponse($interview, 'Interview retrieved successfully');
    }

    /**
     * Update the specified interview.
     */
    public function update(Request $request, Interview $interview)
    {
        if ($interview->company_id !== $request->user()->company_id) {
            return $this->sendForbidden('You do not have permission to update this interview');
        }

        $validator = Validator::make($request->all(), [
            'interviewer_id' => 'sometimes|exists:users,id',
            'type' => 'sometimes|string|in:' . implode(',', array_column(InterviewType::cases(), 'value')),
            'scheduled_at' => 'sometimes|date|after:now',
            'duration_minutes' => 'sometimes|integer|min:15',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $interview->update($request->all());

        return $this->sendResponse($interview, 'Interview updated successfully');
    }

    /**
     * Remove the specified interview.
     */
    public function destroy(Interview $interview)
    {
        if ($interview->company_id !== request()->user()->company_id) {
            return $this->sendForbidden('You do not have permission to delete this interview');
        }

        $interview->delete();

        return $this->sendResponse([], 'Interview deleted successfully');
    }

    /**
     * Update the status of the specified interview.
     */
    public function updateStatus(Request $request, Interview $interview)
    {
        if ($interview->company_id !== $request->user()->company_id) {
            return $this->sendForbidden('You do not have permission to update this interview');
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:' . implode(',', array_column(InterviewStatus::cases(), 'value')),
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $interview->update(['status' => $request->status]);

        return $this->sendResponse($interview, 'Interview status updated successfully');
    }

    /**
     * Submit feedback for the specified interview.
     */
    public function submitFeedback(Request $request, Interview $interview)
    {
        if ($interview->company_id !== $request->user()->company_id) {
            return $this->sendForbidden('You do not have permission to submit feedback for this interview');
        }

        $validator = Validator::make($request->all(), [
            'feedback' => 'required|array',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $interview->update([
            'feedback' => $request->feedback,
            'rating' => $request->rating,
            'status' => InterviewStatus::COMPLETED,
        ]);

        return $this->sendResponse($interview, 'Interview feedback submitted successfully');
    }
} 