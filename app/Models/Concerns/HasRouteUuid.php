<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasRouteUuid
{
    /**
     * Boot the trait and auto-generate a unique UUID on model creation.
     */
    public static function bootHasRouteUuid(): void
    {
        static::creating(function (Model $model): void {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the route key for the model so URLs never expose sequential integer IDs.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
