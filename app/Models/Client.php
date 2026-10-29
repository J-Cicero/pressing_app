<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

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
     * Get the technical visits recorded for this client.
     *
     * @return HasMany<VisiteTechnique, $this>
     */
    public function visitesTechniques(): HasMany
    {
        return $this->hasMany(VisiteTechnique::class);
    }

    /**
     * Count completed paid visits for a specific agency/boutique.
     */
    public function countVisitesParAgence(int $pressingId): int
    {
        return $this->factures()
            ->where('statut', 'paye_retire')
            ->where('pressing_id', $pressingId)
            ->count();
    }

    /**
     * Total completed/paid visits across all agencies (all time).
     */
    public function countTotalVisites(): int
    {
        return $this->factures()->where('statut', 'paye_retire')->count();
    }

    /**
     * Backward-compatible alias for countTotalVisites.
     */
    public function totalVisites(): int
    {
        return $this->countTotalVisites();
    }

    /**
     * Total completed/paid visits for laundry and pressing (all time).
     */
    public function countVisitesPressing(): int
    {
        return $this->factures()
            ->where('statut', 'paye_retire')
            ->whereHas('pressing', fn ($q) => $q->where('type_activite', 'pressing'))
            ->count();
    }

    /**
     * Backward-compatible alias for countVisitesPressing.
     */
    public function visitesPressing(): int
    {
        return $this->countVisitesPressing();
    }

    /**
     * Total completed/paid visits for vehicle washing (all time).
     */
    public function countVisitesLavage(): int
    {
        return $this->factures()
            ->where('statut', 'paye_retire')
            ->whereHas('pressing', fn ($q) => $q->where('type_activite', 'lavage'))
            ->count();
    }

    /**
     * Backward-compatible alias for countVisitesLavage.
     */
    public function visitesLavage(): int
    {
        return $this->countVisitesLavage();
    }

    /**
     * Single Responsibility: Count paid visits performed strictly at or after a specific date.
     */
    public function countVisitesSince(\DateTimeInterface|string $sinceDate, ?string $typeActivite = null): int
    {
        return $this->factures()
            ->where('statut', 'paye_retire')
            ->where('created_at', '>=', $sinceDate)
            ->when($typeActivite, function ($query, $typeActivite) {
                $query->whereHas('pressing', fn ($q) => $q->where('type_activite', $typeActivite));
            })
            ->count();
    }

    /**
     * Single Responsibility: Calculate eligible visits for a given loyalty rule based on its creation date.
     */
    public function countVisitesPourRegle(RegleFidelite $regle): int
    {
        $activite = $regle->type_activite === 'global' ? null : $regle->type_activite;

        // If rule has created_at, count visits from that moment; otherwise count all matching visits
        if ($regle->created_at) {
            return $this->countVisitesSince($regle->created_at, $activite);
        }

        return $activite === 'lavage'
            ? $this->countVisitesLavage()
            : ($activite === 'pressing' ? $this->countVisitesPressing() : $this->countTotalVisites());
    }

    /**
     * Single Responsibility: Determine if client has reached the threshold for a specific loyalty rule.
     */
    public function estEligibleRegle(RegleFidelite $regle): bool
    {
        if (! $regle->actif) {
            return false;
        }

        return $this->countVisitesPourRegle($regle) >= $regle->seuil_visites;
    }

    /**
     * Single Responsibility: Count visits remaining before qualifying for a specific rule.
     */
    public function visitesRestantesPourRegle(RegleFidelite $regle): int
    {
        $current = $this->countVisitesPourRegle($regle);

        return max(0, $regle->seuil_visites - $current);
    }

    /**
     * Single Responsibility: Find highest loyalty rule achieved by client since rule inception.
     */
    public function regleFideliteAtteinte(?string $typeActivite = null): ?RegleFidelite
    {
        $regles = RegleFidelite::query()
            ->where('actif', true)
            ->when($typeActivite, function ($q) use ($typeActivite) {
                $q->whereIn('type_activite', [$typeActivite, 'global']);
            })
            ->orderByDesc('seuil_visites')
            ->get();

        foreach ($regles as $regle) {
            if ($this->estEligibleRegle($regle)) {
                return $regle;
            }
        }

        return null;
    }

    /**
     * Backward-compatible alias for regleFideliteAtteinte.
     */
    public function fideliteAtteinte(?string $typeActivite = null): ?RegleFidelite
    {
        return $this->regleFideliteAtteinte($typeActivite);
    }

    /**
     * Single Responsibility: Find the next unreached loyalty rule closest to being unlocked.
     */
    public function prochaineRegleFidelite(?string $typeActivite = null): ?RegleFidelite
    {
        $regles = RegleFidelite::query()
            ->where('actif', true)
            ->when($typeActivite, function ($q) use ($typeActivite) {
                $q->whereIn('type_activite', [$typeActivite, 'global']);
            })
            ->orderBy('seuil_visites')
            ->get();

        foreach ($regles as $regle) {
            if (! $this->estEligibleRegle($regle)) {
                return $regle;
            }
        }

        return null;
    }

    /**
     * Get all loyalty advantages awarded to this client.
     *
     * @return HasMany<ClientFideliteAvantage, $this>
     */
    public function avantagesFidelite(): HasMany
    {
        return $this->hasMany(ClientFideliteAvantage::class);
    }

    /**
     * Get all currently valid and available loyalty advantages for this client.
     */
    public function avantagesDisponibles(?string $typeActivite = null): Collection
    {
        $this->debloquerAvantagesEligibles($typeActivite);

        return $this->avantagesFidelite()
            ->where('statut', 'disponible')
            ->where('utilisations_restantes', '>', 0)
            ->where(function ($q) {
                $q->whereNull('date_expiration')
                    ->orWhere('date_expiration', '>', now());
            })
            ->whereHas('regleFidelite', function ($q) use ($typeActivite) {
                $q->where('actif', true);
                if ($typeActivite) {
                    $q->whereIn('type_activite', [$typeActivite, 'global']);
                }
            })
            ->with('regleFidelite')
            ->orderByDesc('id')
            ->get();
    }

    /**
     * Inspect all active loyalty rules and unlock newly qualified rewards.
     *
     * @return list<ClientFideliteAvantage> Newly unlocked advantages
     */
    public function debloquerAvantagesEligibles(?string $typeActivite = null): array
    {
        return DB::transaction(function () use ($typeActivite): array {
            static::whereKey($this->id)->lockForUpdate()->first();

            $regles = RegleFidelite::query()
                ->where('actif', true)
                ->when($typeActivite, function ($q) use ($typeActivite) {
                    $q->whereIn('type_activite', [$typeActivite, 'global']);
                })
                ->get();

            $nouveaux = [];

            foreach ($regles as $regle) {
                if (! $this->estEligibleRegle($regle)) {
                    continue;
                }

                // Check if advantage already granted and active, or if already granted for this tier
                $existant = $this->avantagesFidelite()
                    ->where('regle_fidelite_id', $regle->id)
                    ->where(function ($q) {
                        $q->where('statut', 'disponible')
                            ->orWhere('created_at', '>=', now()->subHours(12));
                    })
                    ->lockForUpdate()
                    ->first();

                if (! $existant) {
                    $dureeJours = $regle->duree_jours;
                    $dateExp = $dureeJours ? now()->addDays($dureeJours) : null;
                    $quota = $regle->nombre_utilisations ?: 1;

                    $avantage = ClientFideliteAvantage::create([
                        'client_id' => $this->id,
                        'regle_fidelite_id' => $regle->id,
                        'statut' => 'disponible',
                        'utilisations_restantes' => $quota,
                        'date_expiration' => $dateExp,
                        'debloque_at' => now(),
                    ]);

                    $avantage->setRelation('regleFidelite', $regle);
                    $nouveaux[] = $avantage;
                }
            }

            return $nouveaux;
        });
    }
}
