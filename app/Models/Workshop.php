<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;
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

    protected $hidden = [
        'qr_token',
    ];

    protected static function booted(): void
    {
        static::creating(function (Workshop $workshop) {
            $workshop->qr_token ??= static::newQrToken();
        });
    }

    public static function newQrToken(): string
    {
        do {
            $token = Str::random(40);
        } while (static::where('qr_token', $token)->exists());

        return $token;
    }

    public function regenerateQrToken(): void
    {
        $this->qr_token = static::newQrToken();
        $this->save();
    }

    public function qrUrl(): string
    {
        return route('workshop.scan', ['token' => $this->qr_token]);
    }

    public function workshopMoments(): HasMany
    {
        return $this->hasMany(WorkshopMoment::class);
    }

    public function bookings(): HasManyThrough
    {
        return $this->hasManyThrough(Bookings::class, WorkshopMoment::class, 'workshop_id', 'wm_id');
    }
}
