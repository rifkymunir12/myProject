<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, HasUuids, SoftDeletes;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'post_id',
        'content',
    ];

    /**
     * Get the user that owns the comment.
     */
    public function user()
	{
		return $this->belongsTo(\App\Models\User::class);
	}

    /**
     * Get the post that owns the comment.
     */
    public function post()
	{
		return $this->belongsTo(\App\Models\Post::class);
	}
}

