<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Carbon\Carbon;

class Book extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
    	'ISBN',
    	'name',
    	'description',
    	'publisher_id',
    	'publish_date',
    	'author_id',
    	'shelf',
        'type_id',
    	'book_classification'
    ];

    public function type(){
        return $this->belongsTo('App\Models\Type','type_id','id');
    }

    public function getPublishAgeAttribute(){
        $diff = Carbon::now()->diff($this->publish_date);
        return "{$diff->y}歲{$diff->m}月";    
    }

    public function author(){
        return $this->belongsTo('App\Models\Author');
    }

    public function likes(){
        return $this->belongsToMany('App\Models\User','user_book_likes')->withTimestamps();
    }
}
