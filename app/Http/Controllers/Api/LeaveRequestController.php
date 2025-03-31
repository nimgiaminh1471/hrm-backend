<?php

namespace App\Http\Controllers\Api;

use App\Models\LeaveRequest;
use App\Enums\LeaveRequestStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LeaveRequestController extends BaseController
{
    /**
     * Display a listing of the leave requests.
     */
    public function index(Request $request)
    {
        $query = LeaveRequest::query();

        // Apply filters
        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->has('leave_type_id')) {
            $query->where('leave_type_id', $request->leave_type_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->dateRange($request->start_date, $request->end_date);
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
        $leaveRequests = $query->paginate($perPage);

        return $this->sendResponse($leaveRequests, 'Leave requests retrieved successfully.');
    }

    /**
     * Store a newly created leave request.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:users,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'duration_days' => 'required|numeric|min:0.5',
            'reason' => 'required|string',
            'status' => 'required|in:' . implode(',', array_column(LeaveRequestStatus::cases(), 'value')),
            'is_active' => 'boolean',
            'notes' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $leaveRequest = LeaveRequest::create($request->all());

        return $this->sendResponse($leaveRequest, 'Leave request created successfully.', 201);
    }

    /**
     * Display the specified leave request.
     */
    public function show(LeaveRequest $leaveRequest)
    {
        return $this->sendResponse($leaveRequest, 'Leave request retrieved successfully.');
    }

    /**
     * Update the specified leave request.
     */
    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'sometimes|required|exists:users,id',
            'leave_type_id' => 'sometimes|required|exists:leave_types,id',
            'start_date' => 'sometimes|required|date|after_or_equal:today',
            'end_date' => 'sometimes|required|date|after_or_equal:start_date',
            'duration_days' => 'sometimes|required|numeric|min:0.5',
            'reason' => 'sometimes|required|string',
            'status' => 'sometimes|required|in:' . implode(',', array_column(LeaveRequestStatus::cases(), 'value')),
            'is_active' => 'boolean',
            'notes' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $leaveRequest->update($request->all());

        return $this->sendResponse($leaveRequest, 'Leave request updated successfully.');
    }

    /**
     * Remove the specified leave request.
     */
    public function destroy(LeaveRequest $leaveRequest)
    {
        $leaveRequest->delete();
        return $this->sendResponse([], 'Leave request deleted successfully.');
    }

    /**
     * Approve leave request.
     */
    public function approve(Request $request, LeaveRequest $leaveRequest)
    {
        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $leaveRequest->update([
            'status' => LeaveRequestStatus::APPROVED,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'notes' => $request->notes,
        ]);

        return $this->sendResponse($leaveRequest, 'Leave request approved successfully.');
    }

    /**
     * Reject leave request.
     */
    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'required|string',
            'notes' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $leaveRequest->update([
            'status' => LeaveRequestStatus::REJECTED,
            'rejection_reason' => $request->rejection_reason,
            'rejected_at' => now(),
            'notes' => $request->notes,
        ]);

        return $this->sendResponse($leaveRequest, 'Leave request rejected successfully.');
    }

    /**
     * Cancel leave request.
     */
    public function cancel(Request $request, LeaveRequest $leaveRequest)
    {
        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $leaveRequest->update([
            'status' => LeaveRequestStatus::CANCELLED,
            'notes' => $request->notes,
        ]);

        return $this->sendResponse($leaveRequest, 'Leave request cancelled successfully.');
    }
} 