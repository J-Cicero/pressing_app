<?php

namespace App\Http\Controllers\Caisse;

use App\Http\Controllers\Controller;
use App\Models\Vehicule;
use App\Services\WhatsAppNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CaisseVehiculeController extends Controller
{
    /**
     * Display vehicle technical deadlines for cashier with WhatsApp actions.
     */
    public function echeances(Request $request, WhatsAppNotificationService $whatsApp): View
    {
        $user = Auth::user()->load('pressing');
        $pressing = $user->pressing;

        if (! $pressing) {
            abort(403, 'Aucune agence assignée.');
        }

        $filtre = $request->query('filtre', 'tous');
        $today = Carbon::today();

        $vehiculesQuery = Vehicule::with('client')
            ->whereNotNull('date_visite_technique')
            ->orderBy('date_visite_technique');

        if ($filtre === 'jour_j') {
            $vehiculesQuery->whereDate('date_visite_technique', $today);
        } elseif ($filtre === 'j_3') {
            $vehiculesQuery->whereDate('date_visite_technique', $today->copy()->addDays(3));
        } elseif ($filtre === 'j_7') {
            $vehiculesQuery->whereDate('date_visite_technique', $today->copy()->addDays(7));
        } elseif ($filtre === 'retard') {
            $vehiculesQuery->whereDate('date_visite_technique', '<', $today);
        } elseif ($filtre === 'prochains') {
            $vehiculesQuery->whereBetween('date_visite_technique', [$today, $today->copy()->addDays(30)]);
        }

        $vehicules = $vehiculesQuery->paginate(20)->withQueryString();

        $countJourJ = Vehicule::whereDate('date_visite_technique', $today)->count();
        $countJ3 = Vehicule::whereDate('date_visite_technique', $today->copy()->addDays(3))->count();
        $countJ7 = Vehicule::whereDate('date_visite_technique', $today->copy()->addDays(7))->count();
        $countRetard = Vehicule::whereDate('date_visite_technique', '<', $today)->count();

        $vehicules->getCollection()->transform(function ($v) use ($whatsApp) {
            $diff = $v->joursAvantVisiteTechnique();

            if ($diff < 0) {
                $v->whatsapp_url = $whatsApp->urlRappelVisiteJourJ($v);
                $v->echeance_label = 'Expirée (Retard)';
                $v->echeance_badge = 'urgent';
            } elseif ($diff === 0) {
                $v->whatsapp_url = $whatsApp->urlRappelVisiteJourJ($v);
                $v->echeance_label = 'Expire Aujourd\'hui (Jour J)';
                $v->echeance_badge = 'jour_j';
            } elseif ($diff <= 3) {
                $v->whatsapp_url = $whatsApp->urlRappelVisiteJMoins3($v);
                $v->echeance_label = "Dans {$diff} jour(s) (J-3)";
                $v->echeance_badge = 'j_3';
            } elseif ($diff <= 7) {
                $v->whatsapp_url = $whatsApp->urlRappelVisiteJMoins7($v);
                $v->echeance_label = "Dans {$diff} jours (J-7)";
                $v->echeance_badge = 'j_7';
            } else {
                $v->whatsapp_url = $whatsApp->urlRappelVisiteJMoins7($v);
                $v->echeance_label = "Dans {$diff} jours";
                $v->echeance_badge = 'futur';
            }

            return $v;
        });

        return view('caisse.vehicules.echeances', [
            'pressing' => $pressing,
            'vehicules' => $vehicules,
            'filtre' => $filtre,
            'countJourJ' => $countJourJ,
            'countJ3' => $countJ3,
            'countJ7' => $countJ7,
            'countRetard' => $countRetard,
        ]);
    }
}
