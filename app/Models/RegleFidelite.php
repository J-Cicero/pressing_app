<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RegleFidelite extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'regles_fidelite';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'seuil_visites',
        'type_avantage',
        'valeur_avantage',
        'nombre_utilisations',
        'duree_jours',
        'description',
        'type_activite',
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
            'seuil_visites' => 'integer',
            'valeur_avantage' => 'decimal:2',
            'nombre_utilisations' => 'integer',
            'duree_jours' => 'integer',
            'actif' => 'boolean',
        ];
    }

    /**
     * Get advantages granted under this rule.
     */
    public function avantages(): HasMany
    {
        return $this->hasMany(ClientFideliteAvantage::class, 'regle_fidelite_id');
    }

    /**
     * Calculate discount amount for a given gross total.
     */
    public function calculerRemise(float $montantBrut): float
    {
        if ($montantBrut <= 0) {
            return 0.0;
        }

        return match ($this->type_avantage) {
            'pourcentage' => round($montantBrut * ((float) $this->valeur_avantage / 100), 2),
            'service_offert' => $montantBrut, // 100% de remise sur le lavage ou la prestation
            'montant_fixe' => min($montantBrut, (float) $this->valeur_avantage),
            default => 0.0,
        };
    }
}
