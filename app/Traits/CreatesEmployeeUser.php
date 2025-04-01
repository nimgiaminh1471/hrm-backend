<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Employee;
use App\Models\TenantUser;
use Illuminate\Support\Facades\Hash;

trait CreatesEmployeeUser
{
    /**
     * Create a user with employee record for a company.
     *
     * @param array $userData
     * @param array $employeeData
     * @param int $companyId
     * @return User
     */
    protected function createEmployeeUser(array $userData, array $employeeData, int $companyId): User
    {
        // Create the user
        $user = User::create([
            'name' => $userData['first_name'] . ' ' . $userData['last_name'],
            'email' => $userData['email'],
            'password' => Hash::make($userData['password'] ?? 'password'),
        ]);

        // Create tenant user relationship
        TenantUser::create([
            'user_id' => $user->id,
            'company_id' => $companyId,
            'role' => $employeeData['role'] ?? 'employee',
        ]);

        // Create employee record
        Employee::create([
            'company_id' => $companyId,
            'user_id' => $user->id,
            'department_id' => $employeeData['department_id'] ?? null,
            'position_id' => $employeeData['position_id'] ?? null,
            'employee_id' => $employeeData['employee_id'] ?? null,
            'first_name' => $userData['first_name'],
            'last_name' => $userData['last_name'],
            'email' => $userData['email'],
            'phone' => $employeeData['phone'] ?? null,
            'date_of_birth' => $employeeData['date_of_birth'] ?? null,
            'gender' => $employeeData['gender'] ?? null,
            'address' => $employeeData['address'] ?? null,
            'emergency_contact_name' => $employeeData['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $employeeData['emergency_contact_phone'] ?? null,
            'joining_date' => $employeeData['joining_date'] ?? now(),
            'contract_start_date' => $employeeData['contract_start_date'] ?? null,
            'contract_end_date' => $employeeData['contract_end_date'] ?? null,
            'bank_name' => $employeeData['bank_name'] ?? null,
            'bank_account_number' => $employeeData['bank_account_number'] ?? null,
            'tax_id' => $employeeData['tax_id'] ?? null,
            'social_security_number' => $employeeData['social_security_number'] ?? null,
            'documents' => $employeeData['documents'] ?? null,
            'additional_info' => $employeeData['additional_info'] ?? null,
            'is_active' => true,
        ]);

        return $user;
    }
} 