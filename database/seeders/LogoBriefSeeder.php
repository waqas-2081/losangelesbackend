<?php

namespace Database\Seeders;

use App\Models\LogoBrief;
use Illuminate\Database\Seeder;

class LogoBriefSeeder extends Seeder
{
    public function run(): void
    {
        $briefs = [
            [
                'name'                 => 'John Smith',
                'email'                => 'john.smith@example.com',
                'personal_phone'       => '+1-555-0101',
                'company_phone'        => '+1-555-0102',
                'logo_name'            => 'TechNova',
                'company_slogan'       => 'Innovate Beyond Limits',
                'industry'             => 'Technology',
                'business_desc'        => 'Software development company specializing in AI and machine learning solutions.',
                'logo_description'     => 'Modern, clean logo with a tech feel. Something futuristic with abstract shapes.',
                'competitors_ref'      => 'https://openai.com',
                'competitors_ref_two'  => 'https://anthropic.com',
                'competitors_ref_three'=> null,
                'logo_type'            => 'Combination Mark',
                'logo_fonts'           => 'modern',
                'logo_color'           => 'blue',
                'primary_color'        => '#0072ff',
                'secondary_color'      => '#7c3aed',
                'status'               => 'pending',
                'admin_notes'          => null,
            ],
            [
                'name'                 => 'Sarah Johnson',
                'email'                => 'sarah@greenleaf.com',
                'personal_phone'       => '+1-555-0201',
                'company_phone'        => null,
                'logo_name'            => 'Green Leaf Organics',
                'company_slogan'       => 'Nature at Its Best',
                'industry'             => 'Food & Beverage',
                'business_desc'        => 'Organic food brand selling natural and healthy products online and in retail.',
                'logo_description'     => 'Natural, earthy logo with a leaf element. Friendly and approachable feel.',
                'competitors_ref'      => 'https://wholefoods.com',
                'competitors_ref_two'  => 'https://sprouts.com',
                'competitors_ref_three'=> null,
                'logo_type'            => 'Emblem',
                'logo_fonts'           => 'serif',
                'logo_color'           => 'green',
                'primary_color'        => '#22c55e',
                'secondary_color'      => '#a3a3a3',
                'status'               => 'in_progress',
                'admin_notes'          => 'Client wants earthy tones. Working on 3 concepts.',
            ],
            [
                'name'                 => 'Mike Rodriguez',
                'email'                => 'mike@buildright.com',
                'personal_phone'       => '+1-555-0301',
                'company_phone'        => '+1-555-0302',
                'logo_name'            => 'BuildRight Construction',
                'company_slogan'       => 'Built to Last',
                'industry'             => 'Construction',
                'business_desc'        => 'General contractor handling residential and commercial construction projects in Texas.',
                'logo_description'     => 'Strong, bold logo conveying strength and reliability. Maybe a house or building icon.',
                'competitors_ref'      => 'https://pulte.com',
                'competitors_ref_two'  => null,
                'competitors_ref_three'=> null,
                'logo_type'            => 'Symbol / Icon',
                'logo_fonts'           => 'bold',
                'logo_color'           => 'red',
                'primary_color'        => '#dc2626',
                'secondary_color'      => '#1f2937',
                'status'               => 'completed',
                'admin_notes'          => 'Delivered 3 final files. Client approved on 2nd revision.',
            ],
            [
                'name'                 => 'Emily Chen',
                'email'                => 'emily@luxespa.com',
                'personal_phone'       => '+1-555-0401',
                'company_phone'        => '+1-555-0402',
                'logo_name'            => 'Luxe Spa & Wellness',
                'company_slogan'       => 'Indulge in Serenity',
                'industry'             => 'Beauty & Wellness',
                'business_desc'        => 'High-end spa offering premium beauty treatments and relaxation services.',
                'logo_description'     => 'Elegant and luxurious. Minimalist with gold accents. Very feminine.',
                'competitors_ref'      => 'https://givenchy.com',
                'competitors_ref_two'  => 'https://elemis.com',
                'competitors_ref_three'=> 'https://loccitane.com',
                'logo_type'            => 'Wordmark',
                'logo_fonts'           => 'comic',
                'logo_color'           => 'blue',
                'primary_color'        => '#ec4899',
                'secondary_color'      => '#ffd700',
                'status'               => 'pending',
                'admin_notes'          => null,
            ],
            [
                'name'                 => 'David Park',
                'email'                => 'david@fastfitness.com',
                'personal_phone'       => '+1-555-0501',
                'company_phone'        => null,
                'logo_name'            => 'FastFit Gym',
                'company_slogan'       => 'Train Hard. Live Strong.',
                'industry'             => 'Fitness & Sports',
                'business_desc'        => 'Modern gym chain with 5 locations targeting young professionals.',
                'logo_description'     => 'Dynamic and energetic. Bold typography with motion/speed feel.',
                'competitors_ref'      => 'https://orangetheory.com',
                'competitors_ref_two'  => 'https://f45training.com',
                'competitors_ref_three'=> null,
                'logo_type'            => 'Lettermark',
                'logo_fonts'           => 'bold',
                'logo_color'           => 'red',
                'primary_color'        => '#f7374f',
                'secondary_color'      => '#0072ff',
                'status'               => 'rejected',
                'admin_notes'          => 'Client went with another agency.',
            ],
        ];

        foreach ($briefs as $data) {
            LogoBrief::create($data);
        }

        $this->command->info('✅ LogoBriefSeeder: ' . count($briefs) . ' records created.');
    }
}