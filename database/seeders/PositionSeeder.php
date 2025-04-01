<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            $departments = Department::where('company_id', $company->id)->get();

            foreach ($departments as $department) {
                // Create positions based on department
                $positions = [];

                // Executive positions
                if ($department->name === 'Executive') {
                    $positions = [
                        [
                            'title' => 'Chief Executive Officer',
                            'description' => 'Top executive responsible for overall company management',
                            'department_id' => $department->id,
                            'level' => 'C-Suite',
                            'is_active' => true,
                        ],
                        [
                            'title' => 'Chief Financial Officer',
                            'description' => 'Senior financial executive',
                            'department_id' => $department->id,
                            'level' => 'C-Suite',
                            'is_active' => true,
                        ],
                        [
                            'title' => 'Chief Technology Officer',
                            'description' => 'Senior technology executive',
                            'department_id' => $department->id,
                            'level' => 'C-Suite',
                            'is_active' => true,
                        ],
                    ];
                }
                // HR positions
                elseif ($department->name === 'Human Resources') {
                    $positions = [
                        [
                            'title' => 'HR Director',
                            'description' => 'Head of HR department',
                            'department_id' => $department->id,
                            'level' => 'Senior Management',
                            'is_active' => true,
                        ],
                        [
                            'title' => 'HR Manager',
                            'description' => 'HR department manager',
                            'department_id' => $department->id,
                            'level' => 'Management',
                            'is_active' => true,
                        ],
                        [
                            'title' => 'HR Specialist',
                            'description' => 'HR department specialist',
                            'department_id' => $department->id,
                            'level' => 'Professional',
                            'is_active' => true,
                        ],
                    ];
                }
                // Technology positions
                elseif ($department->name === 'Technology' || $department->name === 'Software Development') {
                    $positions = [
                        [
                            'title' => 'Technical Director',
                            'description' => 'Head of technical department',
                            'department_id' => $department->id,
                            'level' => 'Senior Management',
                            'is_active' => true,
                        ],
                        [
                            'title' => 'Senior Software Engineer',
                            'description' => 'Senior software developer',
                            'department_id' => $department->id,
                            'level' => 'Senior Professional',
                            'is_active' => true,
                        ],
                        [
                            'title' => 'Software Engineer',
                            'description' => 'Software developer',
                            'department_id' => $department->id,
                            'level' => 'Professional',
                            'is_active' => true,
                        ],
                    ];
                }
                // Finance positions
                elseif ($department->name === 'Finance' || $department->name === 'Accounting') {
                    $positions = [
                        [
                            'title' => 'Finance Director',
                            'description' => 'Head of finance department',
                            'department_id' => $department->id,
                            'level' => 'Senior Management',
                            'is_active' => true,
                        ],
                        [
                            'title' => 'Senior Accountant',
                            'description' => 'Senior accounting professional',
                            'department_id' => $department->id,
                            'level' => 'Senior Professional',
                            'is_active' => true,
                        ],
                        [
                            'title' => 'Accountant',
                            'description' => 'Accounting professional',
                            'department_id' => $department->id,
                            'level' => 'Professional',
                            'is_active' => true,
                        ],
                    ];
                }
                // Operations positions
                elseif ($department->name === 'Operations') {
                    $positions = [
                        [
                            'title' => 'Operations Director',
                            'description' => 'Head of operations department',
                            'department_id' => $department->id,
                            'level' => 'Senior Management',
                            'is_active' => true,
                        ],
                        [
                            'title' => 'Operations Manager',
                            'description' => 'Operations department manager',
                            'department_id' => $department->id,
                            'level' => 'Management',
                            'is_active' => true,
                        ],
                        [
                            'title' => 'Operations Specialist',
                            'description' => 'Operations department specialist',
                            'department_id' => $department->id,
                            'level' => 'Professional',
                            'is_active' => true,
                        ],
                    ];
                }

                // Create positions
                foreach ($positions as $positionData) {
                    Position::create([
                        'company_id' => $company->id,
                        'title' => $positionData['title'],
                        'description' => $positionData['description'],
                        'department_id' => $positionData['department_id'],
                        'level' => $positionData['level'],
                        'is_active' => $positionData['is_active'],
                    ]);
                }
            }
        }
    }
} 