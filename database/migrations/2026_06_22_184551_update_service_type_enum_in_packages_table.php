<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Widen the enum to accept BOTH old and new values temporarily,
        //    so existing rows don't break while we migrate the data.
        DB::statement("ALTER TABLE packages MODIFY service_type ENUM(
            'logo',
            'website',
            'animation',
            'mobile_apps',
            'digital_marketing',
            'seo',
            'logo-design-services',
            'website-design-development-services',
            'video-animation-services',
            'mobile-app-development-services',
            'social-media-marketing-services',
            'search-engine-optimization-services'
        ) NOT NULL");

        // 2. Update existing rows from old values to new values.
        $map = [
            'logo' => 'logo-design-services',
            'website' => 'website-design-development-services',
            'animation' => 'video-animation-services',
            'mobile_apps' => 'mobile-app-development-services',
            'digital_marketing' => 'social-media-marketing-services',
            'seo' => 'search-engine-optimization-services',
        ];

        foreach ($map as $old => $new) {
            DB::table('packages')->where('service_type', $old)->update(['service_type' => $new]);
        }

        // 3. Narrow the enum down to ONLY the new values now that data is clean.
        DB::statement("ALTER TABLE packages MODIFY service_type ENUM(
            'logo-design-services',
            'website-design-development-services',
            'video-animation-services',
            'mobile-app-development-services',
            'social-media-marketing-services',
            'search-engine-optimization-services'
        ) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Widen again to allow both, map back, then narrow to old enum.
        DB::statement("ALTER TABLE packages MODIFY service_type ENUM(
            'logo',
            'website',
            'animation',
            'mobile_apps',
            'digital_marketing',
            'seo',
            'logo-design-services',
            'website-design-development-services',
            'video-animation-services',
            'mobile-app-development-services',
            'social-media-marketing-services',
            'search-engine-optimization-services'
        ) NOT NULL");

        $map = [
            'logo-design-services' => 'logo',
            'website-design-development-services' => 'website',
            'video-animation-services' => 'animation',
            'mobile-app-development-services' => 'mobile_apps',
            'social-media-marketing-services' => 'digital_marketing',
            'search-engine-optimization-services' => 'seo',
        ];

        foreach ($map as $old => $new) {
            DB::table('packages')->where('service_type', $old)->update(['service_type' => $new]);
        }

        DB::statement("ALTER TABLE packages MODIFY service_type ENUM(
            'logo',
            'website',
            'animation',
            'mobile_apps',
            'digital_marketing',
            'seo'
        ) NOT NULL");
    }
};