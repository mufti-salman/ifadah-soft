<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic Starter',
                'slug' => 'basic-starter',
                'description' => 'Essential features for small madrasas.',
                'price_monthly' => 1200,
                'price_yearly' => 11520,
                'annual_discount' => 20,
                'student_limit' => 200,
                'sms_limit' => 500,
                'trial_days' => 7,
                'features' => [
                    'Student & Teacher Profiles',
                    'Fee Collection',
                    'Receipt Printing',
                    'Daily Income & Expense',
                    '500 SMS / Year',
                    'Phone & Email Support',
                ],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Standard Pro',
                'slug' => 'standard-pro',
                'description' => 'Advanced management features for growing madrasas.',
                'price_monthly' => 2500,
                'price_yearly' => 24000,
                'annual_discount' => 20,
                'student_limit' => 500,
                'sms_limit' => 1500,
                'trial_days' => 7,
                'features' => [
                    'Student & Teacher Profiles',
                    'Fee Collection',
                    'Receipt Printing',
                    'Full Ledger & Sub-ledger',
                    'Advanced Cash & Bank Reports',
                    'SMS Integration',
                    'ID Card & Certificate Printing',
                    'Priority Support',
                ],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Premium Enterprise',
                'slug' => 'premium-enterprise',
                'description' => 'Comprehensive management features for large institutions.',
                'price_monthly' => 4500,
                'price_yearly' => 43200,
                'annual_discount' => 20,
                'student_limit' => 1200,
                'sms_limit' => 5000,
                'trial_days' => 7,
                'features' => [
                    'All Standard Pro Features',
                    'Advanced Result Management',
                    'Automatic Marksheet',
                    'Digital Attendance',
                    'Payroll System',
                    'Custom Subdomain',
                    'Custom Branding',
                    '5000 SMS / Year',
                    'Dedicated Support',
                ],
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }
    }
}