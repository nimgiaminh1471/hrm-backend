<?php

namespace App\Http\Controllers\Api;

use App\Models\Interview;
use App\Enums\InterviewStatus;
use App\Enums\InterviewType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InterviewController extends BaseController
{
    /**
     * Display a listing of the interviews.
     */
    public function index(Request $request)
    {
        $query = Interview::query();

        // Apply filters
        if ($request->has('candidate_id')) {
            $query->where('candidate_id', $request->candidate_id);
        }

        if ($request->has('interviewer_id')) {
            $query->where('interviewer_id', $request->interviewer_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->scheduledBetween($request->start_date, $request->end_date);
        }

        // Apply search
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Apply sorting
        $sortField = $request->get('sort_by', 'scheduled_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Paginate results
        $perPage = $request->get('per_page', 15);
        $interviews = $query->paginate($perPage);

        return $this->sendResponse($interviews, 'Interviews retrieved successfully.');
    }

    /**
     * Store a newly created interview.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'candidate_id' => 'required|exists:candidates,id',
            'interviewer_id' => 'required|exists:users,id',
            'type' => 'required|in:' . implode(',', array_column(InterviewType::cases(), 'value')),
            'status' => 'required|in:' . implode(',', array_column(InterviewStatus::cases(), 'value')),
            'scheduled_at' => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:15',
            'location' => 'required|string|max:255',
            'notes' => 'nullable|array',
            'feedback' => 'nullable|array',
            'rating' => 'nullable|integer|min:1|max:5',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $interview = Interview::create($request->all());

        return $this->sendResponse($interview, 'Interview created successfully.', 201);
    }

    /**
     * Display the specified interview.
     */
    public function show(Interview $interview)
    {
        return $this->sendResponse($interview, 'Interview retrieved successfully.');
    }

    /**
     * Update the specified interview.
     */
    public function update(Request $request, Interview $interview)
    {
        $validator = Validator::make($request->all(), [
            'candidate_id' => 'sometimes|required|exists:candidates,id',
            'interviewer_id' => 'sometimes|required|exists:users,id',
            'type' => 'sometimes|required|in:' . implode(',', array_column(InterviewType::cases(), 'value')),
            'status' => 'sometimes|required|in:' . implode(',', array_column(InterviewStatus::cases(), 'value')),
            'scheduled_at' => 'sometimes|required|date|after:now',
            'duration_minutes' => 'sometimes|required|integer|min:15',
            'location' => 'sometimes|required|string|max:255',
            'notes' => 'nullable|array',
            'feedback' => 'nullable|array',
            'rating' => 'nullable|integer|min:1|max:5',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $interview->update($request->all());

        return $this->sendResponse($interview, 'Interview updated successfully.');
    }

    /**
     * Remove the specified interview.
     */
    public function destroy(Interview $interview)
    {
        $interview->delete();
        return $this->sendResponse([], 'Interview deleted successfully.');
    }

    /**
     * Update interview status.
     */
    public function updateStatus(Request $request, Interview $interview)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:' . implode(',', array_column(InterviewStatus::cases(), 'value')),
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $interview->update(['status' => $request->status]);
        return $this->sendResponse($interview, 'Interview status updated successfully.');
    }

    /**
     * Submit interview feedback.
     */
    public function submitFeedback(Request $request, Interview $interview)
    {
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

        return $this->sendResponse($interview, 'Interview feedback submitted successfully.');
    }

    /**
     * Reschedule interview.
     */
    public function reschedule(Request $request, Interview $interview)
    {
        $validator = Validator::make($request->all(), [
            'scheduled_at' => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:15',
            'location' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $interview->update([
            'scheduled_at' => $request->scheduled_at,
            'duration_minutes' => $request->duration_minutes,
            'location' => $request->location,
            'status' => InterviewStatus::RESCHEDULED,
        ]);

        return $this->sendResponse($interview, 'Interview rescheduled successfully.');
    }
} 