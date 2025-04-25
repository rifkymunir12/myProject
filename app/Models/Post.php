<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'content',
    ];

    /**
     * Get the user that owns the post.
     */
    public function user()
	{
		return $this->belongsTo(\App\Models\User::class);
	}

    /**
     * Get the comments for the blog post.
     */
    public function comments()
    {
        return $this->hasMany(\App\Models\Comment::class);
    }
}
