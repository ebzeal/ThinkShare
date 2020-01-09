<?php

namespace App;


use App\Traits\Taggable;
use App\Scopes\DeletedPostsScope;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogPost extends Model
{
    // protected $table = 'blogposts';

    use SoftDeletes, Taggable;

    protected $fillable = ['title', 'content', 'user_id'];

    public function comments() {
        return $this->morphMany('App\Comment', 'commentable')->latest();
    }

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function image() {
        return $this->morphOne('App\Image', 'imageable');
    }


    public function scopeLatest(Builder $query){
        return $query->orderBy(static::CREATED_AT, 'desc');
    }

    public function scopeMostCommented(Builder $query){
        // comments_count
        return $query->withCount('comments')->orderBy('comments_count', 'desc');
    }

    public function scopeLatestWithRelations(Builder $query)
    {
        return $query->latest()
            ->withCount('comments')
            ->with('user')
            ->with('tags');
    }

    public static function boot() {
        static::addGlobalScope(new DeletedPostsScope);
        
        parent::boot();

        static::deleting(function(BlogPost $blogPost) {
            $blogPost->comments()->delete();
            Cache::tags(['blog-post'])->forget("blog-post-{$blogPost->id}");

        });

        static::updating(function(BlogPost $blogPost) {
            Cache::tags(['blog-post'])->forget("blog-post-{$blogPost->id}");
        });

        static::restoring(function (BlogPost $blogPost) {
            $blogPost->comments()->restore();
        });
    }
}
