<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientFideliteAvantage extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'client_fidelite_avantages';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'client_id',
        'regle_fidelite_id',
        'statut',
        'utilisations_restantes',
        'date_expiration',
        'debloque_at',
        'last_used_at',
        'facture_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'utilisations_restantes' => 'integer',
            'date_expiration' => 'datetime',
            'debloque_at' => 'datetime',
            'last_used_at' => 'datetime',
        ];
    }

    /**
     * Get the client this advantage belongs to.
     *
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the loyalty rule that defined this advantage.
     *
     * @return BelongsTo<RegleFidelite, $this>
     */
    public function regleFidelite(): BelongsTo
    {
        return $this->belongsTo(RegleFidelite::class, 'regle_fidelite_id');
    }

    /**
     * Get the invoice where this advantage was used/associated.
     *
     * @return BelongsTo<Facture, $this>
     */
    public function facture(): BelongsTo
    {
        return $this->belongsTo(Facture::class);
    }

    /**
     * Determine if this advantage is currently valid and usable.
     */
    public function estValide(): bool
    {
        if ($this->statut !== 'disponible') {
            return false;
        }

        if ($this->utilisations_restantes <= 0) {
            return false;
        }

        if ($this->date_expiration !== null && $this->date_expiration->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Consume one usage of this advantage for an invoice.
     */
    public function consommer(Facture $facture, float $remise): void
    {
        $this->utilisations_restantes = max(0, $this->utilisations_restantes - 1);
        $this->last_used_at = now();
        $this->facture_id = $facture->id;

        if ($this->utilisations_restantes === 0) {
            $this->statut = 'utilise';
        }

        $this->save();
    }
}
