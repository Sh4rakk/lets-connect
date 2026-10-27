<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use SingleQuote\LaravelApiResource\Traits\HasApi;

class Moment extends Model
{
    use HasApi;

    // de moments tabel heeft geen created_at/updated_at
    public $timestamps = false;

    protected $fillable = [
        'time',
    ];

    public function workshopMoments(): HasMany
    {
        return $this->hasMany(WorkshopMoment::class);
    }
}
