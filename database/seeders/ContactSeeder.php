<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Contact;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contacts = [
            [
                'full_name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'phone_number' => '+1 (555) 123-4567',
                'company_name' => 'Tech Solutions Inc.',
                'project_description' => 'We need a new logo and branding for our tech startup. Looking for modern and minimalist design.',
                'status' => 'pending',
                'admin_notes' => null,
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'full_name' => 'Sarah Johnson',
                'email' => 'sarah.j@creativeagency.com',
                'phone_number' => '+1 (555) 987-6543',
                'company_name' => 'Creative Agency LLC',
                'project_description' => 'Looking for a complete website redesign with e-commerce functionality. Need it in 3 months.',
                'status' => 'in_progress',
                'admin_notes' => 'Initial consultation scheduled for next week.',
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(2),
            ],
            [
                'full_name' => 'Michael Chen',
                'email' => 'michael.chen@financecorp.com',
                'phone_number' => '+1 (555) 456-7890',
                'company_name' => 'Finance Corp',
                'project_description' => 'Need professional business cards and letterhead design. Corporate style with gold accents.',
                'status' => 'completed',
                'admin_notes' => 'Project completed successfully. Client very satisfied.',
                'created_at' => now()->subDays(20),
                'updated_at' => now()->subDays(5),
            ],
            [
                'full_name' => 'Emily Rodriguez',
                'email' => 'emily.r@eventplanners.com',
                'phone_number' => '+1 (555) 789-0123',
                'company_name' => 'Event Planners Co.',
                'project_description' => 'Need promotional materials for upcoming event. Flyers, banners, and social media graphics.',
                'status' => 'pending',
                'admin_notes' => null,
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'full_name' => 'David Smith',
                'email' => 'david.smith@restaurant.com',
                'phone_number' => '+1 (555) 234-5678',
                'company_name' => 'Gourmet Restaurant',
                'project_description' => 'New menu design and restaurant branding. Need a fresh look for our new location.',
                'status' => 'pending',
                'admin_notes' => 'Follow up call scheduled for Friday.',
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],
            [
                'full_name' => 'Lisa Thompson',
                'email' => 'lisa.t@realestate.com',
                'phone_number' => '+1 (555) 345-6789',
                'company_name' => 'Premier Real Estate',
                'project_description' => 'Need logo design and branding for new real estate agency. Professional and trustworthy look.',
                'status' => 'in_progress',
                'admin_notes' => 'Client approved initial concepts. Working on revisions.',
                'created_at' => now()->subDays(15),
                'updated_at' => now()->subDays(4),
            ],
            [
                'full_name' => 'Alex Williams',
                'email' => 'alex.w@nonprofit.org',
                'phone_number' => '+1 (555) 567-8901',
                'company_name' => 'Helping Hands Nonprofit',
                'project_description' => 'Need a logo and website design for our charity organization. Focus on community and hope.',
                'status' => 'archived',
                'admin_notes' => 'Client decided to put project on hold. Archived.',
                'created_at' => now()->subDays(30),
                'updated_at' => now()->subDays(15),
            ],
            [
                'full_name' => 'Jessica Martinez',
                'email' => 'jessica.m@fashionstudio.com',
                'phone_number' => '+1 (555) 678-9012',
                'company_name' => 'Fashion Studio',
                'project_description' => 'Branding package for new clothing line. Need logo, tags, and packaging design.',
                'status' => 'completed',
                'admin_notes' => 'All deliverables sent. Client loved the designs.',
                'created_at' => now()->subDays(25),
                'updated_at' => now()->subDays(3),
            ],
            [
                'full_name' => 'Robert Brown',
                'email' => 'robert.b@techstartup.com',
                'phone_number' => '+1 (555) 890-1234',
                'company_name' => 'Tech Startup Inc.',
                'project_description' => 'Need app design and UI/UX for new mobile application. Modern and user-friendly.',
                'status' => 'pending',
                'admin_notes' => null,
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
            [
                'full_name' => 'Michelle Davis',
                'email' => 'michelle.d@wellness.com',
                'phone_number' => '+1 (555) 901-2345',
                'company_name' => 'Wellness Center',
                'project_description' => 'Need logo and marketing materials for new wellness center. Calming and natural design.',
                'status' => 'pending',
                'admin_notes' => 'Client looking for holistic design approach.',
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subDays(4),
            ],
        ];

        foreach ($contacts as $contact) {
            Contact::create($contact);
        }

        $this->command->info('Contacts seeded successfully!');
    }
}