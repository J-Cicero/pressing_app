<?php

namespace App\Models;

use App\Models\Concerns\HasRouteUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicule extends Model
{
    use HasFactory, HasRouteUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'client_id',
        'immatriculation',
        'marque_modele',
        'type_vehicule',
        'date_visite_technique',
        'date_assurance',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_visite_technique' => 'date',
            'date_assurance' => 'date',
        ];
    }

    /**
     * Get the client that owns the vehicle.
     *
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the factures/tickets associated with this vehicle.
     *
     * @return HasMany<Facture, $this>
     */
    public function factures(): HasMany
    {
        return $this->hasMany(Facture::class);
    }

    /**
     * Get technical visit history and records for this vehicle.
     *
     * @return HasMany<VisiteTechnique, $this>
     */
    public function visitesTechniques(): HasMany
    {
        return $this->hasMany(VisiteTechnique::class);
    }

    /**
     * Get the latest technical visit record.
     */
    public function derniereVisiteTechnique(): ?VisiteTechnique
    {
        return $this->visitesTechniques()->latest('date_echeance')->first();
    }

    /**
     * Get the remaining days before technical inspection expires.
     */
    public function joursAvantVisiteTechnique(): ?int
    {
        if (! $this->date_visite_technique) {
            return null;
        }

        $today = Carbon::today();

        return (int) $today->diffInDays($this->date_visite_technique, false);
    }
}
