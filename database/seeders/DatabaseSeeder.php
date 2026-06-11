<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ExperienceCenter;
use App\Models\Lead;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Spatie Roles
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'sales-manager', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'sales-executive', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'experience-center', 'guard_name' => 'web']);

        // Super Admin
        $superAdmin = User::create([
            'name'      => 'Super Admin',
            'email'     => 'admin@y5home.com',
            'password'  => bcrypt('admin@123'),
            'mobile'    => '9000000001',
            'role'      => 'super-admin',
            'is_active' => true,
        ]);
        $superAdmin->assignRole('super-admin');

        // Sales Manager
        $manager = User::create([
            'name'      => 'Sales Manager',
            'email'     => 'manager@y5home.com',
            'password'  => bcrypt('manager@123'),
            'mobile'    => '9000000002',
            'role'      => 'sales-manager',
            'is_active' => true,
        ]);
        $manager->assignRole('sales-manager');

        // Sales Executive
        $executive = User::create([
            'name'      => 'Sales Executive',
            'email'     => 'executive@y5home.com',
            'password'  => bcrypt('exec@123'),
            'mobile'    => '9000000003',
            'role'      => 'sales-executive',
            'is_active' => true,
        ]);
        $executive->assignRole('sales-executive');

        // Experience Centers
        $center1 = ExperienceCenter::create([
            'center_name'             => 'Y5Home Ahmedabad',
            'owner_name'              => 'Rajesh Patel',
            'company_name'            => 'Patel Smart Homes Pvt Ltd',
            'gst_number'              => '24AABCP1234A1Z5',
            'mobile_number'           => '9876543210',
            'email'                   => 'ahmedabad@y5home.com',
            'address'                 => 'SG Highway, Ahmedabad',
            'city'                    => 'Ahmedabad',
            'state'                   => 'Gujarat',
            'country'                 => 'India',
            'agreement_start_date'    => now()->subMonths(6),
            'agreement_end_date'      => now()->addMonths(18),
            'security_deposit_amount' => 100000,
            'status'                  => 'active',
        ]);

        $center2 = ExperienceCenter::create([
            'center_name'             => 'Y5Home Mumbai',
            'owner_name'              => 'Priya Sharma',
            'company_name'            => 'Sharma Automation',
            'mobile_number'           => '9876543211',
            'email'                   => 'mumbai@y5home.com',
            'city'                    => 'Mumbai',
            'state'                   => 'Maharashtra',
            'country'                 => 'India',
            'agreement_start_date'    => now()->subMonths(3),
            'agreement_end_date'      => now()->addMonths(21),
            'security_deposit_amount' => 150000,
            'status'                  => 'active',
        ]);

        // Experience Center User
        $ecUser = User::create([
            'name'                 => 'EC Ahmedabad User',
            'email'                => 'ec.ahmedabad@y5home.com',
            'password'             => bcrypt('ec@123'),
            'mobile'               => '9000000004',
            'role'                 => 'experience-center',
            'experience_center_id' => $center1->id,
            'is_active'            => true,
        ]);
        $ecUser->assignRole('experience-center');

        // Sample Customers and Leads
        $salesExec = User::where('role', 'sales-executive')->first();

        $samples = [
            ['Amit Shah', '9876500001', 'amit@shah.com', 'google_ads', 'apartment', 'finishing', 2500000, 'new', $center1->id],
            ['Meera Joshi', '9876500002', 'meera@joshi.com', 'instagram', 'villa', 'planning', 5000000, 'contacted', $center1->id],
            ['Suresh Kumar', '9876500003', 'suresh@kumar.com', 'referral', 'bungalow', 'construction', 8000000, 'qualified', $center2->id],
            ['Anjali Mehta', '9876500004', 'anjali@mehta.com', 'walk_in', 'apartment', 'ready_possession', 1800000, 'site_visit_completed', $center1->id],
            ['Rahul Gupta', '9876500005', 'rahul@gupta.com', 'builder', 'commercial', 'finishing', 12000000, 'quotation_sent', $center2->id],
        ];

        foreach ($samples as [$name, $mobile, $email, $source, $type, $stage, $budget, $status, $centerId]) {
            // Create Customer record first
            $customer = \App\Models\Customer::create([
                'name'          => $name,
                'mobile_number' => $mobile,
                'email'         => $email,
                'city'          => 'Ahmedabad',
                'state'         => 'Gujarat',
                'country'       => 'India',
                'created_by'    => 1,
            ]);

            // Create Lead record and link it to the Customer
            Lead::create([
                'customer_id'          => $customer->id,
                'customer_name'        => $name,
                'mobile_number'        => $mobile,
                'email'                => $email,
                'lead_source'          => $source,
                'project_type'         => $type,
                'construction_stage'   => $stage,
                'estimated_budget'     => $budget,
                'status'               => $status,
                'city'                 => 'Ahmedabad',
                'state'                => 'Gujarat',
                'country'              => 'India',
                'assigned_to'          => $salesExec->id,
                'experience_center_id' => $centerId,
                'next_followup_date'   => now()->addDays(rand(1, 7))->toDateString(),
                'created_by'           => 1,
            ]);
        }

        echo "✅ Seeded successfully!\n";
        echo "   Admin:     admin@y5home.com / admin@123\n";
        echo "   Manager:   manager@y5home.com / manager@123\n";
        echo "   Executive: executive@y5home.com / exec@123\n";
        echo "   EC User:   ec.ahmedabad@y5home.com / ec@123\n";
    }
}
