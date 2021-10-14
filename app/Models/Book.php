<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
