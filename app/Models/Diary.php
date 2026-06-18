<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diary extends Model
{
    protected $fillable = [
        'user_id', 
        'entry_datetime', 
        'content', 
        'photos'
    ];

    protected $casts = [
        'photos' => 'array',
        'entry_datetime' => 'datetime',
    ];
}
