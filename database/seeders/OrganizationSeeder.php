<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $organizations = [
            [
                'name' => 'Tech Solutions Inc.',
                'subdomain' => 'techsolutions',
                'email' => 'hr@techsolutions.com',
                'phone' => '+1 (555) 123-4567',
                'address' => '123 Tech Street, Silicon Valley, CA 94025',
                'website' => 'https://techsolutions.com',
                'is_active' => true,
            ],
            [
                'name' => 'Global Industries Ltd.',
                'subdomain' => 'globalindustries',
                'email' => 'hr@globalindustries.com',
                'phone' => '+1 (555) 987-6543',
                'address' => '456 Business Ave, New York, NY 10001',
                'website' => 'https://globalindustries.com',
                'is_active' => true,
            ],
            [
                'name' => 'Innovative Systems',
                'subdomain' => 'innovativesystems',
                'email' => 'hr@innovativesystems.com',
                'phone' => '+1 (555) 456-7890',
                'address' => '789 Innovation Blvd, Boston, MA 02108',
                'website' => 'https://innovativesystems.com',
                'is_active' => true,
            ],
        ];

        foreach ($organizations as $organization) {
            Organization::create($organization);
        }
    }
}