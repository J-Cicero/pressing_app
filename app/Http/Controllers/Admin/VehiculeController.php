<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicule;
use App\Services\WhatsAppNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehiculeController extends Controller
{
    /**
     * Display technical visit & insurance deadlines with direct WhatsApp reminders.
     */
    public function echeances(Request $request, WhatsAppNotificationService $whatsApp): View
    {
        $filtre = $request->query('filtre', 'tous'); // 'j_7', 'j_3', 'jour_j', 'retard', 'tous'
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

        // Calculate counts
        $countJourJ = Vehicule::whereDate('date_visite_technique', $today)->count();
        $countJ3 = Vehicule::whereDate('date_visite_technique', $today->copy()->addDays(3))->count();
        $countJ7 = Vehicule::whereDate('date_visite_technique', $today->copy()->addDays(7))->count();
        $countRetard = Vehicule::whereDate('date_visite_technique', '<', $today)->count();

        // Prepare WhatsApp links for each vehicle
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

        return view('admin.vehicules.echeances', [
            'vehicules' => $vehicules,
            'filtre' => $filtre,
            'countJourJ' => $countJourJ,
            'countJ3' => $countJ3,
            'countJ7' => $countJ7,
            'countRetard' => $countRetard,
        ]);
    }
}
