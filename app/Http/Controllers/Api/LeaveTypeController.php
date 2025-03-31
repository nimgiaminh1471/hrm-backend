<?php

namespace App\Http\Controllers\Api;

use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LeaveTypeController extends BaseController
{
    /**
     * Display a listing of the leave types.
     */
    public function index(Request $request)
    {
        $query = LeaveType::query();

        // Apply filters
        if ($request->has('is_paid')) {
            $query->paid($request->boolean('is_paid'));
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Apply search
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Apply sorting
        $sortField = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        // Paginate results
        $perPage = $request->get('per_page', 15);
        $leaveTypes = $query->paginate($perPage);

        return $this->sendResponse($leaveTypes, 'Leave types retrieved successfully.');
    }

    /**
     * Store a newly created leave type.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'default_days' => 'required|numeric|min:0',
            'is_paid' => 'boolean',
            'is_active' => 'boolean',
            'settings' => 'nullable|array',
            'notes' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $leaveType = LeaveType::create($request->all());

        return $this->sendResponse($leaveType, 'Leave type created successfully.', 201);
    }

    /**
     * Display the specified leave type.
     */
    public function show(LeaveType $leaveType)
    {
        return $this->sendResponse($leaveType, 'Leave type retrieved successfully.');
    }

    /**
     * Update the specified leave type.
     */
    public function update(Request $request, LeaveType $leaveType)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'default_days' => 'sometimes|required|numeric|min:0',
            'is_paid' => 'boolean',
            'is_active' => 'boolean',
            'settings' => 'nullable|array',
            'notes' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        $leaveType->update($request->all());

        return $this->sendResponse($leaveType, 'Leave type updated successfully.');
    }

    /**
     * Remove the specified leave type.
     */
    public function destroy(LeaveType $leaveType)
    {
        // Check if leave type has any leave requests
        if ($leaveType->leaveRequests()->exists()) {
            return $this->sendError('Cannot delete leave type with associated leave requests.');
        }

        $leaveType->delete();
        return $this->sendResponse([], 'Leave type deleted successfully.');
    }

    /**
     * Toggle leave type active status.
     */
    public function toggleActive(LeaveType $leaveType)
    {
        $leaveType->update(['is_active' => !$leaveType->is_active]);
        return $this->sendResponse($leaveType, 'Leave type active status updated successfully.');
    }

    /**
     * Toggle leave type paid status.
     */
    public function togglePaid(LeaveType $leaveType)
    {
        $leaveType->update(['is_paid' => !$leaveType->is_paid]);
        return $this->sendResponse($leaveType, 'Leave type paid status updated successfully.');
    }
} 