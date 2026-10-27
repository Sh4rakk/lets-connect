<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';
    protected $fillable = ['key', 'value'];
    public $timestamps = true;

    public static function signupsOpen(): bool
    {
        return static::where('key', 'signups_open')->value('value') === '1';
    }
}
