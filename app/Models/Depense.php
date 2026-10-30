<?php

namespace App\Models;

use App\Models\Concerns\HasRouteUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Depense extends Model
{
    use HasFactory, HasRouteUuid;

    /**
     * Liste des catégories standardisées de dépenses d'agence.
     *
     * @var array<string, string>
     */
    public const CATEGORIES = [
        'produits_lavage' => 'Produits de lavage & Détergents',
        'electricite' => 'Électricité & Énergie',
        'eau' => 'Eau & Assainissement',
        'maintenance' => 'Maintenance & Réparation matériel',
        'fournitures' => 'Fournitures, Cintres & Emballages',
        'loyer' => 'Loyer & Charges locatives',
        'transport' => 'Carburant & Déplacements',
        'autre' => 'Autre charge d\'exploitation',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'pressing_id',
        'user_id',
        'categorie',
        'libelle',
        'montant',
        'date_depense',
        'justificatif',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'montant' => 'decimal:2',
            'date_depense' => 'date',
        ];
    }

    /**
     * Retourne le libellé lisible de la catégorie.
     */
    public function getCategorieLabelAttribute(): string
    {
        return self::CATEGORIES[$this->categorie] ?? ucfirst((string) $this->categorie);
    }
}
