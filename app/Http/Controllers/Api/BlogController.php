<?php
// app/Http/Controllers/Api/BlogController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Blog::published();

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('short_description', 'LIKE', "%{$search}%");
            });
        }

        // Featured filter
        if ($request->boolean('featured')) {
            $query->featured();
        }

        $perPage = $request->get('per_page', 12);
        $blogs = $query->latest('published_at')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $this->formatBlogCollection($blogs),
            'meta' => [
                'current_page' => $blogs->currentPage(),
                'last_page' => $blogs->lastPage(),
                'per_page' => $blogs->perPage(),
                'total' => $blogs->total(),
            ]
        ]);
    }

    public function show($slug)
    {
        $blog = Blog::published()->where('slug', $slug)->first();

        if (!$blog) {
            return response()->json(['success' => false, 'message' => 'Blog not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatBlogDetail($blog)
        ]);
    }

    public function latest()
    {
        $blogs = Blog::published()
            ->latest('published_at')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $this->formatBlogCollection($blogs)
        ]);
    }

    public function featured()
    {
        $blogs = Blog::published()
            ->featured()
            ->latest('published_at')
            ->limit(3)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $this->formatBlogCollection($blogs)
        ]);
    }

    private function formatBlogCollection($blogs)
    {
        return $blogs->map(function ($blog) {
            return $this->formatBlogItem($blog);
        });
    }

    private function formatBlogDetail($blog)
    {
        $data = $this->formatBlogItem($blog);
        $data['content'] = $blog->content;
        $data['meta_title'] = $blog->meta_title;
        $data['meta_description'] = $blog->meta_description;
        $data['meta_keywords'] = $blog->meta_keywords;

        return $data;
    }

    private function formatBlogItem($blog)
    {
        $date = $blog->published_at ?? $blog->created_at;

        return [
            'id' => $blog->id,
            'title' => $blog->title,
            'slug' => $blog->slug,
            'author_name' => $blog->author_name,
            'short_description' => $blog->short_description ?? Str::limit(strip_tags($blog->content), 200),
            'thumbnail' => $blog->thumbnail_url,
            'is_featured' => $blog->is_featured,
            'created_at' => $date->format('Y-m-d H:i:s'),
            'formatted_date' => $date->format('M d, Y'),
            'url' => "/blog/{$blog->slug}",
        ];
    }
}c