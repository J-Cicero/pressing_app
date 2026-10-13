<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nom',
        'telephone',
        'email',
    ];

    /**
     * Get the invoices associated with this client.
     *
     * @return HasMany<Facture, $this>
     */
    public function factures(): HasMany
    {
        return $this->hasMany(Facture::class);
    }

    /**
     * Get the vehicles owned by this client.
     *
     * @return HasMany<Vehicule, $this>
     */
    public function vehicules(): HasMany
    {
        return $this->hasMany(Vehicule::class);
    }

    /**
     * Total completed/paid visits across all agencies.
     */
    public function totalVisites(): int
    {
        return $this->factures()->where('statut', 'paye_retire')->count();
    }

    /**
     * Number of paid visits for laundry/pressing.
     */
    public function visitesPressing(): int
    {
        return $this->factures()
            ->where('statut', 'paye_retire')
            ->whereHas('pressing', fn ($q) => $q->where('type_activite', 'pressing'))
            ->count();
    }

    /**
     * Number of paid visits for car wash.
     */
    public function visitesLavage(): int
    {
        return $this->factures()
            ->where('statut', 'paye_retire')
            ->whereHas('pressing', fn ($q) => $q->where('type_activite', 'lavage'))
            ->count();
    }

    /**
     * Find reached loyalty rule (if any matches current total visits).
     */
    public function fideliteAtteinte(?string $typeActivite = null): ?RegleFidelite
    {
        $visites = $this->totalVisites();

        return RegleFidelite::query()
            ->where('actif', true)
            ->where('seuil_visites', '<=', $visites)
            ->when($typeActivite, function ($q) use ($typeActivite) {
                $q->whereIn('type_activite', [$typeActivite, 'global']);
            })
            ->orderByDesc('seuil_visites')
            ->first();
    }
}
