<?php

namespace App;

use App\Traits\Taggable;
use App\Scopes\LatestScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes, Taggable;

    protected $fillable = ['user_id', 'content'];

      /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at', 'commentable_type', 'commentable_id', 'user_id'
    ];


    public function commentable()
    {
        return $this->morphTo();
    }

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function scopeLatest(Builder $query){
        return $query->orderBy(static::CREATED_AT, 'desc');
    }

}
