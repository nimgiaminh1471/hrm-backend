<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use App\Models\TenantUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create example companies
        $companies = [
            [
                'name' => 'Tech Solutions Inc.',
                'domain' => 'https://techsolutions.example.com',
                'email' => 'contact@techsolutions.example.com',
                'phone' => '+1 (555) 123-4567',
                'address' => '123 Tech Street, Silicon Valley, CA 94025',
                'settings' => [
                    'timezone' => 'America/Los_Angeles',
                    'currency' => 'USD',
                    'date_format' => 'Y-m-d',
                    'time_format' => 'H:i',
                ],
            ],
            [
                'name' => 'Global Industries Ltd.',
                'domain' => 'https://globalindustries.example.com',
                'email' => 'info@globalindustries.example.com',
                'phone' => '+1 (555) 987-6543',
                'address' => '456 Industry Ave, New York, NY 10001',
                'settings' => [
                    'timezone' => 'America/New_York',
                    'currency' => 'USD',
                    'date_format' => 'Y-m-d',
                    'time_format' => 'H:i',
                ],
            ],
            [
                'name' => 'Healthcare Plus',
                'domain' => 'https://healthcareplus.example.com',
                'email' => 'support@healthcareplus.example.com',
                'phone' => '+1 (555) 456-7890',
                'address' => '789 Health Blvd, Boston, MA 02108',
                'settings' => [
                    'timezone' => 'America/New_York',
                    'currency' => 'USD',
                    'date_format' => 'Y-m-d',
                    'time_format' => 'H:i',
                ],
            ],
        ];

        foreach ($companies as $companyData) {
            // Create company
            $company = Company::create($companyData);

            // Create admin user for each company
            $admin = User::create([
                'name' => 'Admin ' . $company->name,
                'email' => 'admin@' . strtolower(str_replace(' ', '', $company->name)) . '.example.com',
                'password' => Hash::make('password123'),
                'company_id' => $company->id,
            ]);

        }
    }
}