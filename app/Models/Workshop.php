<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use SingleQuote\LaravelApiResource\Traits\HasApi;

class Workshop extends Model
{
    use HasUuids, HasApi;

    protected $fillable = [
        'name',
        'full_description',
        'image_url',
        'capacity',
        'location',
    ];

    public function workshopMoments(): HasMany
    {
        return $this->hasMany(WorkshopMoment::class);
    }

    public function bookings(): HasManyThrough
    {
        return $this->hasManyThrough(Bookings::class, WorkshopMoment::class, 'workshop_id', 'wm_id');
    }
}
