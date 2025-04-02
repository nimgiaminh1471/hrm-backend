<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        $organization = $request->organization;
        
        $positions = Position::where('organization_id', $organization->id)
            ->with(['department:id,name,code'])
            ->get();

        return response()->json([
            'positions' => $positions
        ]);
    }

    public function store(Request $request)
    {
        $organization = $request->organization;

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('positions')->where(function ($query) use ($organization) {
                    return $query->where('organization_id', $organization->id);
                }),
            ],
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('positions')->where(function ($query) use ($organization) {
                    return $query->where('organization_id', $organization->id);
                }),
            ],
            'description' => 'nullable|string',
            'department_id' => [
                'required',
                'exists:departments,id',
                function ($attribute, $value, $fail) use ($organization) {
                    $department = \App\Models\Department::find($value);
                    if ($department && $department->organization_id !== $organization->id) {
                        $fail('The department must belong to the same organization.');
                    }
                },
            ],
            'level' => 'required|integer|min:1',
            'min_salary' => 'required|numeric|min:0',
            'max_salary' => 'required|numeric|gt:min_salary',
            'is_active' => 'boolean',
        ]);

        $position = Position::create([
            'organization_id' => $organization->id,
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'department_id' => $request->department_id,
            'level' => $request->level,
            'min_salary' => $request->min_salary,
            'max_salary' => $request->max_salary,
            'is_active' => $request->is_active ?? true,
        ]);

        return response()->json([
            'message' => 'Position created successfully',
            'position' => $position->load(['department:id,name,code'])
        ], 201);
    }

    public function show(Request $request, $subdomain, Position $position)
    {
        $organization = $request->organization;

        if ($position->organization_id !== $organization->id) {
            throw ValidationException::withMessages([
                'position' => ['This position does not belong to your organization.'],
            ]);
        }

        return response()->json([
            'position' => $position->load([
                'department:id,name,code',
                'users:id,name,email,employee_id'
            ])
        ]);
    }

    public function update(Request $request, $subdomain, Position $position)
    {
        $organization = $request->organization;

        if ($position->organization_id !== $organization->id) {
            throw ValidationException::withMessages([
                'position' => ['This position does not belong to your organization.'],
            ]);
        }

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('positions')->where(function ($query) use ($organization) {
                    return $query->where('organization_id', $organization->id);
                })->ignore($position->id),
            ],
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('positions')->where(function ($query) use ($organization) {
                    return $query->where('organization_id', $organization->id);
                })->ignore($position->id),
            ],
            'description' => 'nullable|string',
            'department_id' => [
                'required',
                'exists:departments,id',
                function ($attribute, $value, $fail) use ($organization) {
                    $department = \App\Models\Department::find($value);
                    if ($department && $department->organization_id !== $organization->id) {
                        $fail('The department must belong to the same organization.');
                    }
                },
            ],
            'level' => 'required|integer|min:1',
            'min_salary' => 'required|numeric|min:0',
            'max_salary' => 'required|numeric|gt:min_salary',
            'is_active' => 'boolean',
        ]);

        $position->update([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'department_id' => $request->department_id,
            'level' => $request->level,
            'min_salary' => $request->min_salary,
            'max_salary' => $request->max_salary,
            'is_active' => $request->is_active ?? $position->is_active,
        ]);

        return response()->json([
            'message' => 'Position updated successfully',
            'position' => $position->load(['department:id,name,code'])
        ]);
    }

    public function destroy(Request $request, $subdomain, Position $position)
    {
        $organization = $request->organization;

        if ($position->organization_id !== $organization->id) {
            throw ValidationException::withMessages([
                'position' => ['This position does not belong to your organization.'],
            ]);
        }

        // Check if position has users
        if ($position->users()->exists()) {
            throw ValidationException::withMessages([
                'position' => ['Cannot delete position with assigned users.'],
            ]);
        }

        $position->delete();

        return response()->json([
            'message' => 'Position deleted successfully'
        ]);
    }

    // Get positions by department
    public function getByDepartment(Request $request, $subdomain, $departmentId)
    {
        $organization = $request->organization;

        // Verify department belongs to organization
        $department = \App\Models\Department::where('organization_id', $organization->id)
            ->where('id', $departmentId)
            ->firstOrFail();

        $positions = Position::where('organization_id', $organization->id)
            ->where('department_id', $departmentId)
            ->with(['department:id,name,code'])
            ->get();

        return response()->json([
            'positions' => $positions
        ]);
    }

    // Get active positions
    public function getActive(Request $request, $subdomain)
    {
        $organization = $request->organization;

        $positions = Position::where('organization_id', $organization->id)
            ->where('is_active', true)
            ->with(['department:id,name,code'])
            ->get();

        return response()->json([
            'positions' => $positions
        ]);
    }
} 