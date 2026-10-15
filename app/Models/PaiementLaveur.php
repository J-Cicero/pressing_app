<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaiementLaveur extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'paiements_laveurs';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'laveur_id',
        'mois',
        'montant_total',
        'montant_paye',
        'statut',
        'mode_paiement',
        'date_paiement',
        'paye_par_user_id',
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
            'montant_total' => 'decimal:2',
            'montant_paye' => 'decimal:2',
            'date_paiement' => 'date',
        ];
    }

    /**
     * Get the washer associated with this payment.
     *
     * @return BelongsTo<Laveur, $this>
     */
    public function laveur(): BelongsTo
    {
        return $this->belongsTo(Laveur::class);
    }

    /**
     * Get the user (admin/cashier) who confirmed this physical payment.
     *
     * @return BelongsTo<User, $this>
     */
    public function payePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paye_par_user_id');
    }

    /**
     * Check if payment is fully settled.
     */
    public function estSolde(): bool
    {
        return $this->statut === 'paye';
    }
}
