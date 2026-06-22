<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\LogoBrief;
use App\Models\WebsiteBrief;
use App\Models\Contact;
use App\Models\PromoLead;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'logo_briefs'    => LogoBrief::count(),
            'website_briefs' => WebsiteBrief::count(),
            'contacts'       => Contact::count(),
            'blogs'          => Blog::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}