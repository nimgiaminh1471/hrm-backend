<?php

namespace Database\Seeders;

use App\Enums\EmploymentStatus;
use App\Enums\Gender;
use App\Enums\MaritalStatus;
use App\Models\Department;
use App\Models\Organization;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $organizations = Organization::all();
        
        foreach ($organizations as $organization) {
            // Create admin user for each organization
            User::create([
                'name' => 'Admin User',
                'email' => 'admin@' . $organization->subdomain . '.com',
                'password' => Hash::make('password'),
                'organization_id' => $organization->id,
                'department_id' => Department::where('organization_id', $organization->id)
                    ->where('code', 'HR')
                    ->first()
                    ->id,
                'position_id' => Position::where('organization_id', $organization->id)
                    ->where('code', 'HRM')
                    ->first()
                    ->id,
                'employee_id' => 'EMP' . str_pad($organization->id, 4, '0', STR_PAD_LEFT) . '001',
                'date_of_birth' => '1990-01-01',
                'gender' => Gender::MALE,
                'marital_status' => MaritalStatus::SINGLE,
                'nationality' => 'US',
                'joining_date' => '2023-01-01',
                'employment_status' => EmploymentStatus::ACTIVE,
                'is_active' => true,
            ]);

            // Create sample employees
            $departments = Department::where('organization_id', $organization->id)->get();
            $positions = Position::where('organization_id', $organization->id)->get();

            for ($i = 2; $i <= 10; $i++) {
                $department = $departments->random();
                $position = $positions->random();

                User::create([
                    'name' => 'Employee ' . $i,
                    'email' => 'employee' . $i . '@' . $organization->subdomain . '.com',
                    'password' => Hash::make('password'),
                    'organization_id' => $organization->id,
                    'department_id' => $department->id,
                    'position_id' => $position->id,
                    'employee_id' => 'EMP' . str_pad($organization->id, 4, '0', STR_PAD_LEFT) . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'date_of_birth' => fake()->dateTimeBetween('-40 years', '-25 years')->format('Y-m-d'),
                    'gender' => fake()->randomElement(Gender::cases()),
                    'marital_status' => fake()->randomElement(MaritalStatus::cases()),
                    'nationality' => 'US',
                    'joining_date' => fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
                    'employment_status' => EmploymentStatus::ACTIVE,
                    'is_active' => true,
                ]);
            }
        }
    }
} 