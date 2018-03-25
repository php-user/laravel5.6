<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Post extends Model
{
    protected $fillable = ['user_id', 'title', 'content'];

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function comments()
    {
    	return $this->hasMany(Comment::class);
    }

    public function addComment($userId, $content)
    {
    	$this->comments()->create([
    		'user_id' => $userId,
    		'post_id' => $this->id,
    		'content' => $content,
    	]);
    }

    public function scopeFilter($query, $filters)
    {
    	if (count($filters) > 0) {
    		if ($month = $filters['month']) {
	    		$query->whereMonth('created_at', Carbon::parse($month)->month);
	    	}

	    	if ($year = $filters['year']) {
	    		$query->whereYear('created_at', $year);
	    	}
    	}
    }

    public static function getArchives()
    {
    	return static::selectRaw('year(created_at) year, monthname(created_at) month, count(id) published')
    	                ->groupBy('year', 'month')
    	                ->orderByRaw('min(created_at) asc')
    	                ->get();
    }
}
