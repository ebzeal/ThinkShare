<?php

namespace App;

use App\Scopes\LatestScope;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'content'];

    public function blogPost() {
        return $this->belongsTo('App\BlogPost');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function scopeLatest(Builder $query){
        return $query->orderBy(static::CREATED_AT, 'desc');
    }

    public static function boot() {
        parent::boot();

        static::creating(function(Comment $comment) {
            Cache::tags(['blog-post'])->forget("blog-post-{$comment->blog_post_id}");
            Cache::tags(['blog-post'])->forget('mostCommented');
        });

        // static::addGlobalScope(new LatestScope);
    }
}
