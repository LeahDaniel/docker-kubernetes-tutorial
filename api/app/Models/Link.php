<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'code',
        'url',
        'clicks',
    ];
}
