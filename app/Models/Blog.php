<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class Blog extends Model
{
    use HasFactory, HasApiTokens, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'author_name',
        'short_description',
        'content',
        'thumbnail_image',
        'status',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'is_featured',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($blog) {
            if (empty($blog->slug)) {
                $blog->slug = Str::slug($blog->title);
            }
        });

        static::updating(function ($blog) {
            if ($blog->isDirty('title') && !$blog->isDirty('slug')) {
                $blog->slug = Str::slug($blog->title);
            }
        });
    }

    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail_image 
            ? asset('storage/' . $this->thumbnail_image) 
            : asset('images/default-blog.jpg');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
