<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;


class Post extends Model
{
    use HasFactory;
    protected $table = 'Post';

    public $timestamps = true;
    protected $casts = [
    'price' => 'float'
    ];
    protected $fillable = [
    'title',
    'body',  
    'image'

    ];
    public function category ()
    {
        return $this->belongsTo('\App\Models\Category');
    }
   
}
