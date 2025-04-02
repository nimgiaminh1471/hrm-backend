<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        $organization = $request->organization;
        
        $contracts = Contract::where('organization_id', $organization->id)
            ->with([
                'user:id,name,email,employee_id',
                'position:id,name,code',
                'department:id,name,code'
            ])
            ->get();

        return response()->json([
            'contracts' => $contracts
        ]);
    }

    public function store(Request $request)
    {
        $organization = $request->organization;

        $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($organization) {
                    $user = User::find($value);
                    if ($user && $user->organization_id !== $organization->id) {
                        $fail('The user must belong to the same organization.');
                    }
                },
            ],
            'position_id' => [
                'required',
                'exists:positions,id',
                function ($attribute, $value, $fail) use ($organization) {
                    $position = \App\Models\Position::find($value);
                    if ($position && $position->organization_id !== $organization->id) {
                        $fail('The position must belong to the same organization.');
                    }
                },
            ],
            'contract_number' => 'required|string|unique:contracts,contract_number',
            'type' => 'required|in:full_time,part_time,contract,internship',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'salary' => 'required|numeric|min:0',
            'benefits' => 'nullable|string',
            'terms_and_conditions' => 'nullable|string',
            'status' => 'required|in:active,terminated,expired',
        ]);

        try {
            DB::beginTransaction();

            $contract = Contract::create([
                'organization_id' => $organization->id,
                'user_id' => $request->user_id,
                'position_id' => $request->position_id,
                'contract_number' => $request->contract_number,
                'type' => $request->type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'salary' => $request->salary,
                'benefits' => $request->benefits,
                'terms_and_conditions' => $request->terms_and_conditions,
                'status' => $request->status,
            ]);

            // Update user's position if contract is active
            if ($request->status === 'active') {
                User::where('id', $request->user_id)->update([
                    'position_id' => $request->position_id,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Contract created successfully',
                'contract' => $contract->load([
                    'user:id,name,email,employee_id',
                    'position:id,name,code',
                    'department:id,name,code'
                ])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function show(Request $request, $subdomain, Contract $contract)
    {
        $organization = $request->organization;

        if ($contract->organization_id !== $organization->id) {
            throw ValidationException::withMessages([
                'contract' => ['This contract does not belong to your organization.'],
            ]);
        }

        return response()->json([
            'contract' => $contract->load([
                'user:id,name,email,employee_id',
                'position:id,name,code',
                'department:id,name,code'
            ])
        ]);
    }

    public function update(Request $request, $subdomain, Contract $contract)
    {
        $organization = $request->organization;

        if ($contract->organization_id !== $organization->id) {
            throw ValidationException::withMessages([
                'contract' => ['This contract does not belong to your organization.'],
            ]);
        }

        $request->validate([
            'position_id' => [
                'required',
                'exists:positions,id',
                function ($attribute, $value, $fail) use ($organization) {
                    $position = \App\Models\Position::find($value);
                    if ($position && $position->organization_id !== $organization->id) {
                        $fail('The position must belong to the same organization.');
                    }
                },
            ],
            'contract_number' => [
                'required',
                'string',
                Rule::unique('contracts')->ignore($contract->id),
            ],
            'type' => 'required|in:full_time,part_time,contract,internship',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'salary' => 'required|numeric|min:0',
            'benefits' => 'nullable|string',
            'terms_and_conditions' => 'nullable|string',
            'status' => 'required|in:active,terminated,expired',
        ]);

        try {
            DB::beginTransaction();

            $contract->update([
                'position_id' => $request->position_id,
                'contract_number' => $request->contract_number,
                'type' => $request->type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'salary' => $request->salary,
                'benefits' => $request->benefits,
                'terms_and_conditions' => $request->terms_and_conditions,
                'status' => $request->status,
            ]);

            // Update user's position if contract is active
            if ($request->status === 'active') {
                User::where('id', $contract->user_id)->update([
                    'position_id' => $request->position_id,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Contract updated successfully',
                'contract' => $contract->load([
                    'user:id,name,email,employee_id',
                    'position:id,name,code',
                    'department:id,name,code'
                ])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy(Request $request, $subdomain, Contract $contract)
    {
        $organization = $request->organization;

        if ($contract->organization_id !== $organization->id) {
            throw ValidationException::withMessages([
                'contract' => ['This contract does not belong to your organization.'],
            ]);
        }

        // Only allow deletion of draft contracts
        if ($contract->status !== 'draft') {
            throw ValidationException::withMessages([
                'contract' => ['Only draft contracts can be deleted.'],
            ]);
        }

        $contract->delete();

        return response()->json([
            'message' => 'Contract deleted successfully'
        ]);
    }

    // Get user's contracts
    public function getUserContracts(Request $request, $subdomain, $userId)
    {
        $organization = $request->organization;

        // Verify user belongs to organization
        $user = User::where('organization_id', $organization->id)
            ->where('id', $userId)
            ->firstOrFail();

        $contracts = Contract::where('organization_id', $organization->id)
            ->where('user_id', $userId)
            ->with([
                'user:id,name,email,employee_id',
                'position:id,name,code',
                'department:id,name,code'
            ])
            ->get();

        return response()->json([
            'contracts' => $contracts
        ]);
    }

    // Get active contracts
    public function getActive(Request $request, $subdomain)
    {
        $organization = $request->organization;

        $contracts = Contract::where('organization_id', $organization->id)
            ->where('status', 'active')
            ->with([
                'user:id,name,email,employee_id',
                'position:id,name,code',
                'department:id,name,code'
            ])
            ->get();

        return response()->json([
            'contracts' => $contracts
        ]);
    }

    // Sign contract
    public function sign(Request $request, $subdomain, Contract $contract)
    {
        $organization = $request->organization;

        if ($contract->organization_id !== $organization->id) {
            throw ValidationException::withMessages([
                'contract' => ['This contract does not belong to your organization.'],
            ]);
        }

        $request->validate([
            'signed_by' => 'required|in:employee,employer',
        ]);

        if ($request->signed_by === 'employee') {
            $contract->update([
                'signed_by_employee' => true,
                'signed_date' => now(),
            ]);
        } else {
            $contract->update([
                'signed_by_employer' => true,
                'signed_date' => now(),
            ]);
        }

        // If both parties have signed, activate the contract
        if ($contract->signed_by_employee && $contract->signed_by_employer) {
            $contract->update(['status' => 'active']);

            // Update user's position
            User::where('id', $contract->user_id)->update([
                'position_id' => $contract->position_id,
            ]);
        }

        return response()->json([
            'message' => 'Contract signed successfully',
            'contract' => $contract->load([
                'user:id,name,email,employee_id',
                'position:id,name,code',
                'department:id,name,code'
            ])
        ]);
    }

    // Terminate contract
    public function terminate(Request $request, $subdomain, Contract $contract)
    {
        $organization = $request->organization;

        if ($contract->organization_id !== $organization->id) {
            throw ValidationException::withMessages([
                'contract' => ['This contract does not belong to your organization.'],
            ]);
        }

        $request->validate([
            'termination_date' => 'required|date',
            'termination_reason' => 'required|string',
        ]);

        $contract->update([
            'status' => 'terminated',
            'termination_date' => $request->termination_date,
            'termination_reason' => $request->termination_reason,
        ]);

        return response()->json([
            'message' => 'Contract terminated successfully',
            'contract' => $contract->load([
                'user:id,name,email,employee_id',
                'position:id,name,code',
                'department:id,name,code'
            ])
        ]);
    }
} 