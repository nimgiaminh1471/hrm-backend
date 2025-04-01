<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            // Create main departments
            $departments = [
                [
                    'name' => 'Executive',
                    'description' => 'Executive management team',
                    'parent_id' => null,
                    'is_active' => true,
                ],
                [
                    'name' => 'Human Resources',
                    'description' => 'HR and employee management',
                    'parent_id' => null,
                    'is_active' => true,
                ],
                [
                    'name' => 'Finance',
                    'description' => 'Financial management and accounting',
                    'parent_id' => null,
                    'is_active' => true,
                ],
                [
                    'name' => 'Operations',
                    'description' => 'Business operations and processes',
                    'parent_id' => null,
                    'is_active' => true,
                ],
                [
                    'name' => 'Technology',
                    'description' => 'IT and technical services',
                    'parent_id' => null,
                    'is_active' => true,
                ],
            ];

            foreach ($departments as $departmentData) {
                $department = Department::create([
                    'company_id' => $company->id,
                    'name' => $departmentData['name'],
                    'description' => $departmentData['description'],
                    'parent_id' => $departmentData['parent_id'],
                    'is_active' => $departmentData['is_active'],
                ]);

                // Create sub-departments for some main departments
                if ($department->name === 'Technology') {
                    $subDepartments = [
                        [
                            'name' => 'Software Development',
                            'description' => 'Software engineering and development',
                            'parent_id' => $department->id,
                            'is_active' => true,
                        ],
                        [
                            'name' => 'IT Support',
                            'description' => 'Technical support and maintenance',
                            'parent_id' => $department->id,
                            'is_active' => true,
                        ],
                        [
                            'name' => 'Infrastructure',
                            'description' => 'Network and system infrastructure',
                            'parent_id' => $department->id,
                            'is_active' => true,
                        ],
                    ];

                    foreach ($subDepartments as $subDepartmentData) {
                        Department::create([
                            'company_id' => $company->id,
                            'name' => $subDepartmentData['name'],
                            'description' => $subDepartmentData['description'],
                            'parent_id' => $subDepartmentData['parent_id'],
                            'is_active' => $subDepartmentData['is_active'],
                        ]);
                    }
                }

                if ($department->name === 'Finance') {
                    $subDepartments = [
                        [
                            'name' => 'Accounting',
                            'description' => 'Financial accounting and reporting',
                            'parent_id' => $department->id,
                            'is_active' => true,
                        ],
                        [
                            'name' => 'Payroll',
                            'description' => 'Employee payroll and compensation',
                            'parent_id' => $department->id,
                            'is_active' => true,
                        ],
                        [
                            'name' => 'Tax',
                            'description' => 'Tax management and compliance',
                            'parent_id' => $department->id,
                            'is_active' => true,
                        ],
                    ];

                    foreach ($subDepartments as $subDepartmentData) {
                        Department::create([
                            'company_id' => $company->id,
                            'name' => $subDepartmentData['name'],
                            'description' => $subDepartmentData['description'],
                            'parent_id' => $subDepartmentData['parent_id'],
                            'is_active' => $subDepartmentData['is_active'],
                        ]);
                    }
                }
            }
        }
    }
} 