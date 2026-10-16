<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisiteTechnique extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'visites_techniques';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'vehicule_id',
        'client_id',
        'date_visite',
        'date_echeance',
        'date_assurance',
        'statut',
        'centre_controle',
        'notes',
        'derniere_notification_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_visite' => 'date',
            'date_echeance' => 'date',
            'date_assurance' => 'date',
            'derniere_notification_at' => 'datetime',
        ];
    }

    /**
     * Vehicle associated with this technical visit.
     *
     * @return BelongsTo<Vehicule, $this>
     */
    public function vehicule(): BelongsTo
    {
        return $this->belongsTo(Vehicule::class);
    }

    /**
     * Client owner of the vehicle.
     *
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Number of days until expiration (negative if expired).
     */
    public function joursAvantEcheance(): int
    {
        if (! $this->date_echeance) {
            return 999;
        }

        return (int) Carbon::today()->diffInDays($this->date_echeance, false);
    }

    /**
     * Check if expiration is today.
     */
    public function estAujourdhui(): bool
    {
        return $this->joursAvantEcheance() === 0;
    }

    /**
     * Check if expired.
     */
    public function estExpire(): bool
    {
        return $this->joursAvantEcheance() < 0;
    }
}
