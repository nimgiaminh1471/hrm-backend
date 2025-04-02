<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $organization = $request->organization;
        
        $departments = Department::where('organization_id', $organization->id)
            ->with(['head:id,name,email'])
            ->get();

        return response()->json([
            'departments' => $departments
        ]);
    }

    public function store(Request $request)
    {
        $organization = $request->organization;

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('departments')->where(function ($query) use ($organization) {
                    return $query->where('organization_id', $organization->id);
                }),
            ],
            'description' => 'nullable|string',
            'parent_id' => [
                'nullable',
                'exists:departments,id',
                function ($attribute, $value, $fail) use ($organization) {
                    if ($value) {
                        $parent = Department::find($value);
                        if ($parent && $parent->organization_id !== $organization->id) {
                            $fail('The parent department must belong to the same organization.');
                        }
                    }
                },
            ],
            'head_id' => [
                'nullable',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($organization) {
                    if ($value) {
                        $head = \App\Models\User::find($value);
                        if ($head && $head->organization_id !== $organization->id) {
                            $fail('The department head must belong to the same organization.');
                        }
                    }
                },
            ],
        ]);

        $department = Department::create([
            'organization_id' => $organization->id,
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'parent_id' => $request->parent_id,
            'head_id' => $request->head_id,
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Department created successfully',
            'department' => $department->load(['head:id,name,email'])
        ], 201);
    }

    public function show(Request $request, $subdomain, Department $department)
    {
        $organization = $request->organization;

        if ($department->organization_id !== $organization->id) {
            throw ValidationException::withMessages([
                'department' => ['This department does not belong to your organization.'],
            ]);
        }

        return response()->json([
            'department' => $department->load([
                'head:id,name,email',
                'parent:id,name,code',
                'children:id,name,code,head_id'
            ])
        ]);
    }

    public function update(Request $request, $subdomain, Department $department)
    {
        $organization = $request->organization;

        if ($department->organization_id !== $organization->id) {
            throw ValidationException::withMessages([
                'department' => ['This department does not belong to your organization.'],
            ]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('departments')->where(function ($query) use ($organization) {
                    return $query->where('organization_id', $organization->id);
                })->ignore($department->id),
            ],
            'description' => 'nullable|string',
            'parent_id' => [
                'nullable',
                'exists:departments,id',
                function ($attribute, $value, $fail) use ($organization, $department) {
                    if ($value) {
                        $parent = Department::find($value);
                        if ($parent && $parent->organization_id !== $organization->id) {
                            $fail('The parent department must belong to the same organization.');
                        }
                        if ($value === $department->id) {
                            $fail('A department cannot be its own parent.');
                        }
                    }
                },
            ],
            'head_id' => [
                'nullable',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($organization) {
                    if ($value) {
                        $head = \App\Models\User::find($value);
                        if ($head && $head->organization_id !== $organization->id) {
                            $fail('The department head must belong to the same organization.');
                        }
                    }
                },
            ],
            'is_active' => 'boolean',
        ]);

        $department->update([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'parent_id' => $request->parent_id,
            'head_id' => $request->head_id,
            'is_active' => $request->is_active ?? $department->is_active,
        ]);

        return response()->json([
            'message' => 'Department updated successfully',
            'department' => $department->load(['head:id,name,email'])
        ]);
    }

    public function destroy(Request $request, $subdomain, Department $department)
    {
        $organization = $request->organization;

        if ($department->organization_id !== $organization->id) {
            throw ValidationException::withMessages([
                'department' => ['This department does not belong to your organization.'],
            ]);
        }

        // Check if department has children
        if ($department->children()->exists()) {
            throw ValidationException::withMessages([
                'department' => ['Cannot delete department with sub-departments.'],
            ]);
        }

        // Check if department has employees
        if ($department->users()->exists()) {
            throw ValidationException::withMessages([
                'department' => ['Cannot delete department with employees.'],
            ]);
        }

        $department->delete();

        return response()->json([
            'message' => 'Department deleted successfully'
        ]);
    }
} 