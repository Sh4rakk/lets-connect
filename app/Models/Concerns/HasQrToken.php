<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait HasQrToken
{
    protected static function bootHasQrToken(): void
    {
        static::creating(function ($model) {
            $model->qr_token ??= static::newQrToken();
        });
    }

    public static function newQrToken(): string
    {
        do {
            $token = Str::random(40);
        } while (static::where('qr_token', $token)->exists());

        return $token;
    }

    public function ensureQrToken(): void
    {
        if (! $this->qr_token) {
            $this->regenerateQrToken();
        }
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
}
