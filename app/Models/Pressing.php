<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pressing extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nom',
        'ville',
        'quartier',
        'telephone',
        'type_activite',
    ];

    /**
     * Check if the establishment is a laundry / pressing service.
     */
    public function isPressing(): bool
    {
        return ($this->type_activite ?? 'pressing') === 'pressing';
    }

    /**
     * Check if the establishment is a vehicle wash station.
     */
    public function isLavage(): bool
    {
        return ($this->type_activite ?? 'pressing') === 'lavage';
    }

    /**
     * Get the users associated with the establishment.
     *
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the washers associated with this establishment.
     *
     * @return HasMany<Laveur, $this>
     */
    public function laveurs(): HasMany
    {
        return $this->hasMany(Laveur::class);
    }

    /**
     * Get the services offered by the establishment.
     *
     * @return HasMany<Service, $this>
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Get the factures created for the establishment.
     *
     * @return HasMany<Facture, $this>
     */
    public function factures(): HasMany
    {
        return $this->hasMany(Facture::class);
    }
}
