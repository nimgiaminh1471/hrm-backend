<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Position;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $organization = $request->organization;
        
        $users = User::where('organization_id', $organization->id)
            ->with([
                'department:id,name,code',
                'position:id,name,code',
                'team:id,name,code',
                'manager:id,name,email,employee_id'
            ])
            ->get();

        return response()->json([
            'users' => $users
        ]);
    }

    public function store(Request $request)
    {
        $organization = $request->organization;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->where(function ($query) use ($organization) {
                    return $query->where('organization_id', $organization->id);
                }),
            ],
            'password' => 'required|string|min:8',
            'employee_id' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users')->where(function ($query) use ($organization) {
                    return $query->where('organization_id', $organization->id);
                }),
            ],
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'required|date',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'department_id' => [
                'required',
                'exists:departments,id',
                function ($attribute, $value, $fail) use ($organization) {
                    $department = Department::find($value);
                    if ($department && $department->organization_id !== $organization->id) {
                        $fail('The department must belong to the same organization.');
                    }
                },
            ],
            'position_id' => [
                'required',
                'exists:positions,id',
                function ($attribute, $value, $fail) use ($organization) {
                    $position = Position::find($value);
                    if ($position && $position->organization_id !== $organization->id) {
                        $fail('The position must belong to the same organization.');
                    }
                },
            ],
            'team_id' => [
                'nullable',
                'exists:teams,id',
                function ($attribute, $value, $fail) use ($organization) {
                    if ($value) {
                        $team = Team::find($value);
                        if ($team && $team->organization_id !== $organization->id) {
                            $fail('The team must belong to the same organization.');
                        }
                    }
                },
            ],
            'manager_id' => [
                'nullable',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($organization) {
                    if ($value) {
                        $manager = User::find($value);
                        if ($manager && $manager->organization_id !== $organization->id) {
                            $fail('The manager must belong to the same organization.');
                        }
                    }
                },
            ],
            'hire_date' => 'required|date',
            'employment_status' => 'required|in:active,on_leave,terminated',
            'employment_type' => 'required|in:full_time,part_time,contract',
            'salary' => 'required|numeric|min:0',
            'bank_name' => 'required|string|max:100',
            'bank_account' => 'required|string|max:50',
            'bank_branch' => 'required|string|max:100',
            'tax_id' => 'required|string|max:50',
            'social_security_number' => 'required|string|max:50',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
            'emergency_contact_relationship' => 'required|string|max:100',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'organization_id' => $organization->id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'employee_id' => $request->employee_id,
                'status' => 'active',
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'gender' => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'postal_code' => $request->postal_code,
                'department_id' => $request->department_id,
                'position_id' => $request->position_id,
                'team_id' => $request->team_id,
                'manager_id' => $request->manager_id,
                'hire_date' => $request->hire_date,
                'employment_status' => $request->employment_status,
                'employment_type' => $request->employment_type,
                'salary' => $request->salary,
                'bank_name' => $request->bank_name,
                'bank_account' => $request->bank_account,
                'bank_branch' => $request->bank_branch,
                'tax_id' => $request->tax_id,
                'social_security_number' => $request->social_security_number,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'emergency_contact_relationship' => $request->emergency_contact_relationship,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'User created successfully',
                'user' => $user->load([
                    'department:id,name,code',
                    'position:id,name,code',
                    'team:id,name,code',
                    'manager:id,name,email,employee_id'
                ])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function show(Request $request, $subdomain, User $user)
    {
        $organization = $request->organization;

        if ($user->organization_id !== $organization->id) {
            throw ValidationException::withMessages([
                'user' => ['This user does not belong to your organization.'],
            ]);
        }

        return response()->json([
            'user' => $user->load([
                'department:id,name,code',
                'position:id,name,code',
                'team:id,name,code',
                'manager:id,name,email,employee_id'
            ])
        ]);
    }

    public function update(Request $request, $subdomain, User $user)
    {
        $organization = $request->organization;

        if ($user->organization_id !== $organization->id) {
            throw ValidationException::withMessages([
                'user' => ['This user does not belong to your organization.'],
            ]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->where(function ($query) use ($organization) {
                    return $query->where('organization_id', $organization->id);
                })->ignore($user->id),
            ],
            'password' => 'nullable|string|min:8',
            'employee_id' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users')->where(function ($query) use ($organization) {
                    return $query->where('organization_id', $organization->id);
                })->ignore($user->id),
            ],
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'required|date',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'department_id' => [
                'required',
                'exists:departments,id',
                function ($attribute, $value, $fail) use ($organization) {
                    $department = Department::find($value);
                    if ($department && $department->organization_id !== $organization->id) {
                        $fail('The department must belong to the same organization.');
                    }
                },
            ],
            'position_id' => [
                'required',
                'exists:positions,id',
                function ($attribute, $value, $fail) use ($organization) {
                    $position = Position::find($value);
                    if ($position && $position->organization_id !== $organization->id) {
                        $fail('The position must belong to the same organization.');
                    }
                },
            ],
            'team_id' => [
                'nullable',
                'exists:teams,id',
                function ($attribute, $value, $fail) use ($organization) {
                    if ($value) {
                        $team = Team::find($value);
                        if ($team && $team->organization_id !== $organization->id) {
                            $fail('The team must belong to the same organization.');
                        }
                    }
                },
            ],
            'manager_id' => [
                'nullable',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($organization) {
                    if ($value) {
                        $manager = User::find($value);
                        if ($manager && $manager->organization_id !== $organization->id) {
                            $fail('The manager must belong to the same organization.');
                        }
                    }
                },
            ],
            'hire_date' => 'required|date',
            'employment_status' => 'required|in:active,on_leave,terminated',
            'employment_type' => 'required|in:full_time,part_time,contract',
            'salary' => 'required|numeric|min:0',
            'bank_name' => 'required|string|max:100',
            'bank_account' => 'required|string|max:50',
            'bank_branch' => 'required|string|max:100',
            'tax_id' => 'required|string|max:50',
            'social_security_number' => 'required|string|max:50',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
            'emergency_contact_relationship' => 'required|string|max:100',
        ]);

        try {
            DB::beginTransaction();

            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'employee_id' => $request->employee_id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'gender' => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'postal_code' => $request->postal_code,
                'department_id' => $request->department_id,
                'position_id' => $request->position_id,
                'team_id' => $request->team_id,
                'manager_id' => $request->manager_id,
                'hire_date' => $request->hire_date,
                'employment_status' => $request->employment_status,
                'employment_type' => $request->employment_type,
                'salary' => $request->salary,
                'bank_name' => $request->bank_name,
                'bank_account' => $request->bank_account,
                'bank_branch' => $request->bank_branch,
                'tax_id' => $request->tax_id,
                'social_security_number' => $request->social_security_number,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'emergency_contact_relationship' => $request->emergency_contact_relationship,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            DB::commit();

            return response()->json([
                'message' => 'User updated successfully',
                'user' => $user->load([
                    'department:id,name,code',
                    'position:id,name,code',
                    'team:id,name,code',
                    'manager:id,name,email,employee_id'
                ])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy(Request $request, $subdomain, User $user)
    {
        $organization = $request->organization;

        if ($user->organization_id !== $organization->id) {
            throw ValidationException::withMessages([
                'user' => ['This user does not belong to your organization.'],
            ]);
        }

        try {
            DB::beginTransaction();

            // Soft delete the user
            $user->delete();

            DB::commit();

            return response()->json([
                'message' => 'User deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // Get user by employee ID
    public function getByEmployeeId(Request $request, $subdomain, $employeeId)
    {
        $organization = $request->organization;

        $user = User::where('organization_id', $organization->id)
            ->where('employee_id', $employeeId)
            ->with([
                'department:id,name,code',
                'position:id,name,code',
                'team:id,name,code',
                'manager:id,name,email,employee_id'
            ])
            ->firstOrFail();

        return response()->json([
            'user' => $user
        ]);
    }

    // Update user status
    public function updateStatus(Request $request, $subdomain, User $user)
    {
        $organization = $request->organization;

        if ($user->organization_id !== $organization->id) {
            throw ValidationException::withMessages([
                'user' => ['This user does not belong to your organization.'],
            ]);
        }

        $request->validate([
            'employment_status' => 'required|in:active,on_leave,terminated',
        ]);

        try {
            DB::beginTransaction();

            // Update user status
            $user->update([
                'employment_status' => $request->employment_status,
                'status' => $request->employment_status === 'terminated' ? 'inactive' : 'active'
            ]);

            DB::commit();

            return response()->json([
                'message' => 'User status updated successfully',
                'user' => $user->load([
                    'department:id,name,code',
                    'position:id,name,code',
                    'team:id,name,code',
                    'manager:id,name,email,employee_id'
                ])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}