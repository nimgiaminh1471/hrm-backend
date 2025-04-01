<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Organization;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $organizations = Organization::all();
        
        foreach ($organizations as $organization) {
            $departments = [
                [
                    'name' => 'Engineering',
                    'code' => 'ENG',
                    'description' => 'Software Development and Engineering Department',
                    'is_active' => true,
                ],
                [
                    'name' => 'Human Resources',
                    'code' => 'HR',
                    'description' => 'Human Resources and Employee Management',
                    'is_active' => true,
                ],
                [
                    'name' => 'Finance',
                    'code' => 'FIN',
                    'description' => 'Financial Management and Accounting',
                    'is_active' => true,
                ],
                [
                    'name' => 'Marketing',
                    'code' => 'MKT',
                    'description' => 'Marketing and Communications',
                    'is_active' => true,
                ],
                [
                    'name' => 'Sales',
                    'code' => 'SALES',
                    'description' => 'Sales and Business Development',
                    'is_active' => true,
                ],
                [
                    'name' => 'Operations',
                    'code' => 'OPS',
                    'description' => 'Operations and Project Management',
                    'is_active' => true,
                ],
            ];

            foreach ($departments as $department) {
                Department::create([
                    ...$department,
                    'organization_id' => $organization->id,
                ]);
            }
        }
    }
} 