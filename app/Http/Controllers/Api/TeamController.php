<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $organization = $request->organization;
        
        $teams = Team::where('organization_id', $organization->id)
            ->with(['leader:id,name,email', 'department:id,name,code'])
            ->get();

        return response()->json([
            'teams' => $teams
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
                Rule::unique('teams')->where(function ($query) use ($organization) {
                    return $query->where('organization_id', $organization->id);
                }),
            ],
            'description' => 'nullable|string',
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
            'leader_id' => [
                'nullable',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($organization) {
                    if ($value) {
                        $leader = \App\Models\User::find($value);
                        if ($leader && $leader->organization_id !== $organization->id) {
                            $fail('The team leader must belong to the same organization.');
                        }
                    }
                },
            ],
        ]);

        $team = Team::create([
            'organization_id' => $organization->id,
            'department_id' => $request->department_id,
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'leader_id' => $request->leader_id,
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Team created successfully',
            'team' => $team->load(['leader:id,name,email', 'department:id,name,code'])
        ], 201);
    }

    public function show(Request $request, $subdomain, Team $team)
    {
        $organization = $request->organization;

        if ($team->organization_id !== $organization->id) {
            throw ValidationException::withMessages([
                'team' => ['This team does not belong to your organization.'],
            ]);
        }

        return response()->json([
            'team' => $team->load([
                'leader:id,name,email',
                'department:id,name,code',
                'users:id,name,email,employee_id,position_id'
            ])
        ]);
    }

    public function update(Request $request, $subdomain, Team $team)
    {
        $organization = $request->organization;

        if ($team->organization_id !== $organization->id) {
            throw ValidationException::withMessages([
                'team' => ['This team does not belong to your organization.'],
            ]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('teams')->where(function ($query) use ($organization) {
                    return $query->where('organization_id', $organization->id);
                })->ignore($team->id),
            ],
            'description' => 'nullable|string',
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
            'leader_id' => [
                'nullable',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($organization) {
                    if ($value) {
                        $leader = \App\Models\User::find($value);
                        if ($leader && $leader->organization_id !== $organization->id) {
                            $fail('The team leader must belong to the same organization.');
                        }
                    }
                },
            ],
            'is_active' => 'boolean',
        ]);

        $team->update([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'department_id' => $request->department_id,
            'leader_id' => $request->leader_id,
            'is_active' => $request->is_active ?? $team->is_active,
        ]);

        return response()->json([
            'message' => 'Team updated successfully',
            'team' => $team->load(['leader:id,name,email', 'department:id,name,code'])
        ]);
    }

    public function destroy(Request $request, $subdomain, Team $team)
    {
        $organization = $request->organization;

        if ($team->organization_id !== $organization->id) {
            throw ValidationException::withMessages([
                'team' => ['This team does not belong to your organization.'],
            ]);
        }

        // Check if team has members
        if ($team->users()->exists()) {
            throw ValidationException::withMessages([
                'team' => ['Cannot delete team with members.'],
            ]);
        }

        $team->delete();

        return response()->json([
            'message' => 'Team deleted successfully'
        ]);
    }

    public function addMember(Request $request, $subdomain, Team $team)
    {
        $organization = $request->organization;

        if ($team->organization_id !== $organization->id) {
            throw ValidationException::withMessages([
                'team' => ['This team does not belong to your organization.'],
            ]);
        }

        $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($organization) {
                    $user = \App\Models\User::find($value);
                    if ($user && $user->organization_id !== $organization->id) {
                        $fail('The user must belong to the same organization.');
                    }
                },
            ],
        ]);

        $user = \App\Models\User::find($request->user_id);
        
        // Update user's team
        $user->update(['team_id' => $team->id]);

        return response()->json([
            'message' => 'Team member added successfully',
            'team' => $team->load(['leader:id,name,email', 'department:id,name,code', 'users:id,name,email,employee_id,position_id'])
        ]);
    }

    public function removeMember(Request $request, $subdomain, Team $team)
    {
        $organization = $request->organization;

        if ($team->organization_id !== $organization->id) {
            throw ValidationException::withMessages([
                'team' => ['This team does not belong to your organization.'],
            ]);
        }

        $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($organization) {
                    $user = \App\Models\User::find($value);
                    if ($user && $user->organization_id !== $organization->id) {
                        $fail('The user must belong to the same organization.');
                    }
                },
            ],
        ]);

        $user = \App\Models\User::find($request->user_id);
        
        // Remove user from team
        $user->update(['team_id' => null]);

        return response()->json([
            'message' => 'Team member removed successfully',
            'team' => $team->load(['leader:id,name,email', 'department:id,name,code', 'users:id,name,email,employee_id,position_id'])
        ]);
    }
} 