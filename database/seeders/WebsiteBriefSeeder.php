<?php

namespace Database\Seeders;

use App\Models\WebsiteBrief;
use Illuminate\Database\Seeder;

class WebsiteBriefSeeder extends Seeder
{
    public function run(): void
    {
        $briefs = [
            [
                'name'                 => 'John Smith',
                'email'                => 'john@example.com',
                'business_name'        => 'Smith Plumbing Co.',
                'website_type'         => 'informative_without_payment',
                'products_count'       => '5',
                'services_count_no_payment' => '8',
                'future_images_products' => 'Will provide product images later',
                'business_description' => 'We provide plumbing services in San Jose area.',
                'business_industry'    => 'Plumbing',
                'target_audience'      => 'Local homeowners',
                'overall_feel'         => ['corporate', 'friendly'],
                'competitors_references' => 'roto-rooter.com, benjaminfranklinplumbing.com',
                'has_domain'           => true,
                'pages_count'          => 5,
                'pages_list'           => "Home\nAbout\nServices\nGallery\nContact",
                'has_logo'             => true,
                'wants_logo_revamp'    => false,
                'needs_hosting'        => true,
                'needs_responsive'     => true,
                'addon_features'       => ['seo_optimization', 'custom_forms', 'ssl_certification'],
                'status'               => 'pending',
            ],
            [
                'name'                 => 'Sarah Johnson',
                'email'                => 'sarah@designstudio.com',
                'business_name'        => 'Creative Design Studio',
                'website_type'         => 'informative_with_payment_services',
                'services_count_with_price' => "Logo Design - $299\nBrand Identity - $599\nWeb Design - $999",
                'accept_online_payments' => true,
                'payment_medium'       => 'Stripe',
                'future_images_services' => 'Will send portfolio images next week',
                'business_description' => 'Full-service creative agency specializing in branding and web design.',
                'business_industry'    => 'Design & Creative',
                'target_audience'      => 'Small to medium businesses',
                'overall_feel'         => ['trendy', 'minimal', 'hi-tech'],
                'competitors_references' => '99designs.com, dribbble.com',
                'has_domain'           => true,
                'pages_count'          => 7,
                'pages_list'           => "Home\nAbout\nServices\nPortfolio\nPricing\nBlog\nContact",
                'has_logo'             => true,
                'wants_logo_revamp'    => false,
                'needs_hosting'        => false,
                'needs_responsive'     => true,
                'addon_features'       => ['chat_integration', 'newsletter', 'seo_optimization', 'blogs'],
                'status'               => 'in_progress',
            ],
            [
                'name'                 => 'Mike Davis',
                'email'                => 'mike@davisfitness.com',
                'business_name'        => 'Davis Fitness Center',
                'website_type'         => 'ecommerce',
                'business_description' => 'Premium fitness center offering memberships, classes, and equipment.',
                'business_industry'    => 'Health & Fitness',
                'target_audience'      => 'Fitness enthusiasts aged 18-45',
                'overall_feel'         => ['fun', 'hi-tech', 'dark'],
                'competitors_references' => 'equinox.com, planetfitness.com',
                'has_domain'           => false,
                'pages_count'          => 8,
                'pages_list'           => "Home\nMemberships\nClasses\nTrainers\nShop\nGallery\nBlog\nContact",
                'has_logo'             => false,
                'wants_logo_revamp'    => true,
                'needs_hosting'        => true,
                'needs_responsive'     => true,
                'addon_features'       => ['sign_up_sign_in', 'custom_dashboard', 'newsletter', 'videos_animations', 'ssl_certification'],
                'status'               => 'pending',
            ],
            [
                'name'                 => 'Emily Chen',
                'email'                => 'emily@techsolutions.io',
                'business_name'        => 'Tech Solutions Inc.',
                'website_type'         => 'custom_web_app',
                'business_description' => 'B2B SaaS company providing project management tools.',
                'business_industry'    => 'Technology / SaaS',
                'target_audience'      => 'Project managers and development teams',
                'overall_feel'         => ['corporate', 'hi-tech', 'minimal'],
                'competitors_references' => 'asana.com, monday.com, trello.com',
                'has_domain'           => true,
                'pages_count'          => 10,
                'pages_list'           => "Landing\nFeatures\nPricing\nDocs\nBlog\nLogin\nDashboard\nSettings\nTeam\nContact",
                'has_logo'             => true,
                'wants_logo_revamp'    => false,
                'needs_hosting'        => true,
                'needs_responsive'     => true,
                'addon_features'       => ['database', 'sign_up_sign_in', 'custom_dashboard', 'security_encryption', '3rd_party_api', 'ada_compliance'],
                'status'               => 'completed',
            ],
            [
                'name'                 => 'Robert Martinez',
                'email'                => 'robert@martinezlaw.com',
                'business_name'        => 'Martinez Law Firm',
                'website_type'         => 'informative_without_payment',
                'services_count_no_payment' => '6',
                'future_images_products' => 'Team photos and office photos to be provided',
                'business_description' => 'Family law firm serving the San Jose community for over 20 years.',
                'business_industry'    => 'Legal',
                'target_audience'      => 'Individuals and families needing legal help',
                'overall_feel'         => ['corporate', 'friendly', 'light'],
                'competitors_references' => 'martindale.com, avvo.com',
                'has_domain'           => true,
                'pages_count'          => 6,
                'pages_list'           => "Home\nAbout\nPractice Areas\nAttorneys\nTestimonials\nContact",
                'has_logo'             => true,
                'wants_logo_revamp'    => true,
                'needs_hosting'        => false,
                'needs_responsive'     => true,
                'addon_features'       => ['custom_forms', 'seo_optimization', 'ssl_certification', 'ada_compliance'],
                'status'               => 'rejected',
            ],
        ];

        foreach ($briefs as $data) {
            WebsiteBrief::create($data);
        }

        $this->command->info('✅ WebsiteBrief seeder completed — 5 records inserted.');
    }
}