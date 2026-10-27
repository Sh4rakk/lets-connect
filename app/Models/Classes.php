<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use SingleQuote\LaravelApiResource\Traits\HasApi;

class Classes extends Model
{
    use HasApi;

    protected $fillable = [
        'name',
        'LOB',
        'total_amount',
        'class_group',
        'shared_total',
    ];
}
