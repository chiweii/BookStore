<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
    	'ISBN',
    	'name',
    	'description',
    	'publisher_id',
    	'publish_date',
    	'author_id',
    	'shelf',
    	'book_classification'
    ];
}
