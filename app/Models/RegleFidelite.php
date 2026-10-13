<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
            'actif' => 'boolean',
        ];
    }
}
