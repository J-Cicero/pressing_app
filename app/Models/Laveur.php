<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Laveur extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'pressing_id',
        'nom',
        'telephone',
        'type_remuneration',
        'valeur_remuneration',
        'actif',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'valeur_remuneration' => 'decimal:2',
            'actif' => 'boolean',
        ];
    }

    /**
     * Get the establishment that employs this washer.
     *
     * @return BelongsTo<Pressing, $this>
     */
    public function pressing(): BelongsTo
    {
        return $this->belongsTo(Pressing::class);
    }

    /**
     * Get the invoices/tickets processed by this washer.
     *
     * @return HasMany<Facture, $this>
     */
    public function factures(): HasMany
    {
        return $this->hasMany(Facture::class);
    }

    /**
     * Compute salary or commission for a given invoice.
     */
    public function calculerCommission(Facture $facture): float
    {
        if ($this->type_remuneration === 'pourcentage') {
            return round(((float) $facture->montant_total * (float) $this->valeur_remuneration) / 100, 2);
        }

        return (float) $this->valeur_remuneration;
    }

    /**
     * Calculate total commissions earned on a given date.
     */
    public function commissionsDate(Carbon|string $date): float
    {
        $factures = $this->factures()
            ->whereDate('created_at', $date)
            ->get();

        return $factures->sum(fn (Facture $f) => $this->calculerCommission($f));
    }
}
