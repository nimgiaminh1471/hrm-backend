<?php

namespace App\Http\Controllers\Api;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class DepartmentController extends BaseController
{
    /**
     * Display a listing of departments.
     */
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Department::class)
            ->allowedFilters([
                AllowedFilter::exact('parent_id'),
                AllowedFilter::exact('head_id'),
                AllowedFilter::partial('name'),
                AllowedFilter::scope('active'),
            ])
            ->allowedSorts([
                AllowedSort::field('name'),
                AllowedSort::field('created_at'),
            ])
            ->with(['parent', 'head'])
            ->where('company_id', $request->user()->company_id)
            ->latest();

        $result = $query->paginate($request->get('per_page', 15));

        return $this->sendResponse($result, 'Departments retrieved successfully');
    }

    /**
     * Store a newly created department.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'parent_id' => 'nullable|exists:departments,id',
            'head_id' => 'nullable|exists:users,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'settings' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        // Check if parent department belongs to company
        if ($request->parent_id) {
            $parentDepartment = Department::findOrFail($request->parent_id);
            if ($parentDepartment->company_id !== $request->user()->company_id) {
                return $this->sendForbidden('You do not have permission to use this parent department');
            }
        }

        $department = Department::create([
            'company_id' => $request->user()->company_id,
            'parent_id' => $request->parent_id,
            'head_id' => $request->head_id,
            'name' => $request->name,
            'description' => $request->description,
            'settings' => $request->settings,
        ]);

        return $this->sendResponse($department, 'Department created successfully');
    }

    /**
     * Display the specified department.
     */
    public function show(Department $department)
    {
        if ($department->company_id !== request()->user()->company_id) {
            return $this->sendForbidden('You do not have permission to view this department');
        }

        $department->load(['parent', 'head', 'employees', 'positions']);

        return $this->sendResponse($department, 'Department retrieved successfully');
    }

    /**
     * Update the specified department.
     */
    public function update(Request $request, Department $department)
    {
        if ($department->company_id !== $request->user()->company_id) {
            return $this->sendForbidden('You do not have permission to update this department');
        }

        $validator = Validator::make($request->all(), [
            'parent_id' => 'nullable|exists:departments,id',
            'head_id' => 'nullable|exists:users,id',
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'settings' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        // Check if parent department belongs to company
        if ($request->parent_id) {
            $parentDepartment = Department::findOrFail($request->parent_id);
            if ($parentDepartment->company_id !== $request->user()->company_id) {
                return $this->sendForbidden('You do not have permission to use this parent department');
            }
        }

        $department->update($request->all());

        return $this->sendResponse($department, 'Department updated successfully');
    }

    /**
     * Remove the specified department.
     */
    public function destroy(Department $department)
    {
        if ($department->company_id !== request()->user()->company_id) {
            return $this->sendForbidden('You do not have permission to delete this department');
        }

        // Check if department has employees or positions
        if ($department->employees()->exists() || $department->positions()->exists()) {
            return $this->sendError('Cannot delete department with employees or positions');
        }

        $department->delete();

        return $this->sendResponse([], 'Department deleted successfully');
    }

    /**
     * Toggle active status of the department.
     */
    public function toggleActive(Department $department)
    {
        if ($department->company_id !== request()->user()->company_id) {
            return $this->sendForbidden('You do not have permission to update this department');
        }

        $department->update(['is_active' => !$department->is_active]);

        return $this->sendResponse($department, 'Department status updated successfully');
    }

    /**
     * Get department hierarchy.
     */
    public function hierarchy(Department $department)
    {
        if ($department->company_id !== request()->user()->company_id) {
            return $this->sendForbidden('You do not have permission to view this department hierarchy');
        }

        $hierarchy = $department->load(['children.children.children']);

        return $this->sendResponse($hierarchy, 'Department hierarchy retrieved successfully');
    }
} 