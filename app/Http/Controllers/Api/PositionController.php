<?php

namespace App\Http\Controllers\Api;

use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class PositionController extends BaseController
{
    /**
     * Display a listing of positions.
     */
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Position::class)
            ->allowedFilters([
                AllowedFilter::exact('department_id'),
                AllowedFilter::exact('level'),
                AllowedFilter::partial('title'),
                AllowedFilter::scope('active'),
            ])
            ->allowedSorts([
                AllowedSort::field('title'),
                AllowedSort::field('level'),
                AllowedSort::field('created_at'),
            ])
            ->with(['department'])
            ->where('company_id', $request->user()->company_id)
            ->latest();

        $result = $query->paginate($request->get('per_page', 15));

        return $this->sendResponse($result, 'Positions retrieved successfully');
    }

    /**
     * Store a newly created position.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'department_id' => 'required|exists:departments,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level' => 'required|integer|min:1',
            'responsibilities' => 'required|array',
            'requirements' => 'required|array',
            'settings' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        // Check if department belongs to company
        $department = Department::findOrFail($request->department_id);
        if ($department->company_id !== $request->user()->company_id) {
            return $this->sendForbidden('You do not have permission to create positions in this department');
        }

        $position = Position::create([
            'company_id' => $request->user()->company_id,
            'department_id' => $request->department_id,
            'title' => $request->title,
            'description' => $request->description,
            'level' => $request->level,
            'responsibilities' => $request->responsibilities,
            'requirements' => $request->requirements,
            'settings' => $request->settings,
        ]);

        return $this->sendResponse($position, 'Position created successfully');
    }

    /**
     * Display the specified position.
     */
    public function show(Position $position)
    {
        if ($position->company_id !== request()->user()->company_id) {
            return $this->sendForbidden('You do not have permission to view this position');
        }

        $position->load(['department', 'employees']);

        return $this->sendResponse($position, 'Position retrieved successfully');
    }

    /**
     * Update the specified position.
     */
    public function update(Request $request, Position $position)
    {
        if ($position->company_id !== $request->user()->company_id) {
            return $this->sendForbidden('You do not have permission to update this position');
        }

        $validator = Validator::make($request->all(), [
            'department_id' => 'sometimes|exists:departments,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'level' => 'sometimes|integer|min:1',
            'responsibilities' => 'sometimes|array',
            'requirements' => 'sometimes|array',
            'settings' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        // Check if department belongs to company
        if ($request->has('department_id')) {
            $department = Department::findOrFail($request->department_id);
            if ($department->company_id !== $request->user()->company_id) {
                return $this->sendForbidden('You do not have permission to move this position to this department');
            }
        }

        $position->update($request->all());

        return $this->sendResponse($position, 'Position updated successfully');
    }

    /**
     * Remove the specified position.
     */
    public function destroy(Position $position)
    {
        if ($position->company_id !== request()->user()->company_id) {
            return $this->sendForbidden('You do not have permission to delete this position');
        }

        // Check if position has employees
        if ($position->employees()->exists()) {
            return $this->sendError('Cannot delete position with employees');
        }

        $position->delete();

        return $this->sendResponse([], 'Position deleted successfully');
    }

    /**
     * Toggle active status of the position.
     */
    public function toggleActive(Position $position)
    {
        if ($position->company_id !== request()->user()->company_id) {
            return $this->sendForbidden('You do not have permission to update this position');
        }

        $position->update(['is_active' => !$position->is_active]);

        return $this->sendResponse($position, 'Position status updated successfully');
    }
} 