<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Facture extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'num_ticket',
        'pressing_id',
        'user_id',
        'client_id',
        'vehicule_id',
        'laveur_id',
        'client_nom',
        'client_telephone',
        'client_email',
        'montant_brut',
        'remise_fidelite',
        'regle_fidelite_id',
        'client_fidelite_avantage_id',
        'details_remise',
        'montant_total',
        'statut',
        'date_retrait_prevue',
        'paye_at',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'num_ticket';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'montant_brut' => 'decimal:2',
            'remise_fidelite' => 'decimal:2',
            'montant_total' => 'decimal:2',
            'date_retrait_prevue' => 'date',
            'paye_at' => 'datetime',
        ];
    }

    /**
     * Get the pressing / establishment that the invoice belongs to.
     *
     * @return BelongsTo<Pressing, $this>
     */
    public function pressing(): BelongsTo
    {
        return $this->belongsTo(Pressing::class);
    }

    /**
     * Get the user who created the invoice.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the client associated with the invoice.
     *
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the vehicle associated with this wash invoice (if car wash).
     *
     * @return BelongsTo<Vehicule, $this>
     */
    public function vehicule(): BelongsTo
    {
        return $this->belongsTo(Vehicule::class);
    }

    /**
     * Get the assigned washer (terrain personnel).
     *
     * @return BelongsTo<Laveur, $this>
     */
    public function laveur(): BelongsTo
    {
        return $this->belongsTo(Laveur::class);
    }

    /**
     * Get the line items for the invoice.
     *
     * @return HasMany<LigneFacture, $this>
     */
    public function ligneFactures(): HasMany
    {
        return $this->hasMany(LigneFacture::class);
    }

    /**
     * Check if the invoice is for a car/moto wash service.
     */
    public function isLavage(): bool
    {
        if ($this->relationLoaded('pressing') && $this->pressing) {
            return $this->pressing->isLavage();
        }

        if ($this->pressing_id) {
            $pressing = Pressing::find($this->pressing_id);
            if ($pressing) {
                return $pressing->isLavage();
            }
        }

        return $this->vehicule_id !== null;
    }

    /**
     * Check if the invoice is for pressing/laundry service.
     */
    public function isPressing(): bool
    {
        return ! $this->isLavage();
    }

    /**
     * Get the loyalty rule applied to this invoice.
     *
     * @return BelongsTo<RegleFidelite, $this>
     */
    public function regleFidelite(): BelongsTo
    {
        return $this->belongsTo(RegleFidelite::class, 'regle_fidelite_id');
    }

    /**
     * Get the client loyalty advantage used for this invoice.
     *
     * @return BelongsTo<ClientFideliteAvantage, $this>
     */
    public function fideliteAvantage(): BelongsTo
    {
        return $this->belongsTo(ClientFideliteAvantage::class, 'client_fidelite_avantage_id');
    }

    /**
     * Check if a loyalty discount was applied to this invoice.
     */
    public function hasRemiseFidelite(): bool
    {
        return (float) $this->remise_fidelite > 0;
    }
}
