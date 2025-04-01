<?php

namespace App\Http\Controllers\Api;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class EmployeeController extends BaseController
{
    /**
     * Display a listing of employees.
     */
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Employee::class)
            ->allowedFilters([
                AllowedFilter::exact('department_id'),
                AllowedFilter::exact('position_id'),
                AllowedFilter::partial('first_name'),
                AllowedFilter::partial('last_name'),
                AllowedFilter::partial('email'),
                AllowedFilter::partial('employee_id'),
                AllowedFilter::scope('active'),
            ])
            ->allowedSorts([
                AllowedSort::field('first_name'),
                AllowedSort::field('last_name'),
                AllowedSort::field('email'),
                AllowedSort::field('employee_id'),
                AllowedSort::field('joining_date'),
                AllowedSort::field('created_at'),
            ])
            ->with(['department', 'position'])
            ->where('company_id', $request->user()->company_id)
            ->latest();

        $result = $query->paginate($request->get('per_page', 15));

        return $this->sendResponse($result, 'Employees retrieved successfully');
    }

    /**
     * Store a newly created employee.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'employee_id' => 'required|string|max:50|unique:employees,employee_id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|in:male,female,other',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'joining_date' => 'required|date',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after:contract_start_date',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'tax_id' => 'nullable|string|max:50',
            'social_security_number' => 'nullable|string|max:50',
            'documents' => 'nullable|array',
            'additional_info' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        // Check if department belongs to company
        $department = Department::findOrFail($request->department_id);
        if ($department->company_id !== $request->user()->company_id) {
            return $this->sendForbidden('You do not have permission to add employees to this department');
        }

        // Check if position belongs to company
        $position = Position::findOrFail($request->position_id);
        if ($position->company_id !== $request->user()->company_id) {
            return $this->sendForbidden('You do not have permission to assign this position');
        }

        $employee = Employee::create([
            'company_id' => $request->user()->company_id,
            'user_id' => $request->user_id,
            'department_id' => $request->department_id,
            'position_id' => $request->position_id,
            'employee_id' => $request->employee_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'address' => $request->address,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_phone' => $request->emergency_contact_phone,
            'joining_date' => $request->joining_date,
            'contract_start_date' => $request->contract_start_date,
            'contract_end_date' => $request->contract_end_date,
            'bank_name' => $request->bank_name,
            'bank_account_number' => $request->bank_account_number,
            'tax_id' => $request->tax_id,
            'social_security_number' => $request->social_security_number,
            'documents' => $request->documents,
            'additional_info' => $request->additional_info,
        ]);

        return $this->sendResponse($employee, 'Employee created successfully');
    }

    /**
     * Display the specified employee.
     */
    public function show(Employee $employee)
    {
        if ($employee->company_id !== request()->user()->company_id) {
            return $this->sendForbidden('You do not have permission to view this employee');
        }

        $employee->load(['department', 'position', 'user']);

        return $this->sendResponse($employee, 'Employee retrieved successfully');
    }

    /**
     * Update the specified employee.
     */
    public function update(Request $request, Employee $employee)
    {
        if ($employee->company_id !== $request->user()->company_id) {
            return $this->sendForbidden('You do not have permission to update this employee');
        }

        $validator = Validator::make($request->all(), [
            'department_id' => 'sometimes|exists:departments,id',
            'position_id' => 'sometimes|exists:positions,id',
            'employee_id' => 'sometimes|string|max:50|unique:employees,employee_id,' . $employee->id,
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|in:male,female,other',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'joining_date' => 'sometimes|date',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after:contract_start_date',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'tax_id' => 'nullable|string|max:50',
            'social_security_number' => 'nullable|string|max:50',
            'documents' => 'nullable|array',
            'additional_info' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->sendValidationError($validator->errors());
        }

        // Check if department belongs to company
        if ($request->has('department_id')) {
            $department = Department::findOrFail($request->department_id);
            if ($department->company_id !== $request->user()->company_id) {
                return $this->sendForbidden('You do not have permission to move this employee to this department');
            }
        }

        // Check if position belongs to company
        if ($request->has('position_id')) {
            $position = Position::findOrFail($request->position_id);
            if ($position->company_id !== $request->user()->company_id) {
                return $this->sendForbidden('You do not have permission to assign this position');
            }
        }

        $employee->update($request->all());

        return $this->sendResponse($employee, 'Employee updated successfully');
    }

    /**
     * Remove the specified employee.
     */
    public function destroy(Employee $employee)
    {
        if ($employee->company_id !== request()->user()->company_id) {
            return $this->sendForbidden('You do not have permission to delete this employee');
        }

        $employee->delete();

        return $this->sendResponse([], 'Employee deleted successfully');
    }

    /**
     * Toggle active status of the employee.
     */
    public function toggleActive(Employee $employee)
    {
        if ($employee->company_id !== request()->user()->company_id) {
            return $this->sendForbidden('You do not have permission to update this employee');
        }

        $employee->update(['is_active' => !$employee->is_active]);

        return $this->sendResponse($employee, 'Employee status updated successfully');
    }
} 