<?php

namespace Database\Seeders;

use App\Enums\PositionLevel;
use App\Models\Organization;
use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $organizations = Organization::all();
        
        foreach ($organizations as $organization) {
            $positions = [
                // Engineering Positions
                [
                    'title' => 'Software Engineer',
                    'code' => 'SWE',
                    'description' => 'Develop and maintain software applications',
                    'responsibilities' => 'Write clean, maintainable code, participate in code reviews, collaborate with team members',
                    'requirements' => 'Bachelor\'s degree in Computer Science, 2+ years of experience',
                    'base_salary' => 80000,
                    'level' => PositionLevel::MID,
                    'is_active' => true,
                ],
                [
                    'title' => 'Senior Software Engineer',
                    'code' => 'SSWE',
                    'description' => 'Lead software development initiatives',
                    'responsibilities' => 'Lead technical decisions, mentor junior developers, architect solutions',
                    'requirements' => 'Bachelor\'s degree in Computer Science, 5+ years of experience',
                    'base_salary' => 120000,
                    'level' => PositionLevel::SENIOR,
                    'is_active' => true,
                ],
                [
                    'title' => 'Engineering Manager',
                    'code' => 'EM',
                    'description' => 'Manage engineering team and projects',
                    'responsibilities' => 'Team management, project planning, technical strategy',
                    'requirements' => 'Bachelor\'s degree in Computer Science, 8+ years of experience',
                    'base_salary' => 150000,
                    'level' => PositionLevel::MANAGER,
                    'is_active' => true,
                ],
                // HR Positions
                [
                    'title' => 'HR Specialist',
                    'code' => 'HRS',
                    'description' => 'Handle HR operations and employee relations',
                    'responsibilities' => 'Recruitment, employee relations, HR policies',
                    'requirements' => 'Bachelor\'s degree in HR or related field, 2+ years of experience',
                    'base_salary' => 60000,
                    'level' => PositionLevel::MID,
                    'is_active' => true,
                ],
                [
                    'title' => 'HR Manager',
                    'code' => 'HRM',
                    'description' => 'Lead HR department and initiatives',
                    'responsibilities' => 'HR strategy, team management, organizational development',
                    'requirements' => 'Bachelor\'s degree in HR or related field, 5+ years of experience',
                    'base_salary' => 90000,
                    'level' => PositionLevel::MANAGER,
                    'is_active' => true,
                ],
                // Finance Positions
                [
                    'title' => 'Financial Analyst',
                    'code' => 'FA',
                    'description' => 'Analyze financial data and prepare reports',
                    'responsibilities' => 'Financial analysis, reporting, budgeting',
                    'requirements' => 'Bachelor\'s degree in Finance or Accounting, 2+ years of experience',
                    'base_salary' => 70000,
                    'level' => PositionLevel::MID,
                    'is_active' => true,
                ],
                [
                    'title' => 'Finance Manager',
                    'code' => 'FM',
                    'description' => 'Manage financial operations and strategy',
                    'responsibilities' => 'Financial planning, team management, risk management',
                    'requirements' => 'Bachelor\'s degree in Finance or Accounting, 5+ years of experience',
                    'base_salary' => 100000,
                    'level' => PositionLevel::MANAGER,
                    'is_active' => true,
                ],
            ];

            foreach ($positions as $position) {
                Position::create([
                    ...$position,
                    'organization_id' => $organization->id,
                ]);
            }
        }
    }
} 