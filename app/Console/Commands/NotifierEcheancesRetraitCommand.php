<?php

namespace App\Console\Commands;

use App\Mail\TicketEcheanceRetraitMail;
use App\Models\Facture;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifierEcheancesRetraitCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pressing:notifier-echeances-retrait';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoie les e-mails de notification aux clients de pressing dont la date de retrait est échue ce jour';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $today = Carbon::today();

        // Récupérer les tickets pressing dont la date de retrait est aujourd'hui et non encore retirés
        $factures = Facture::whereIn('statut', ['depose', 'pret'])
            ->whereDate('date_retrait_prevue', $today)
            ->whereNotNull('client_email')
            ->whereHas('pressing', fn ($q) => $q->where('type_activite', 'pressing'))
            ->with(['pressing', 'ligneFactures.service', 'client'])
            ->get();

        $this->info("{$factures->count()} ticket(s) pressing échu(s) trouvé(s) pour aujourd'hui.");

        $envoyes = 0;
        foreach ($factures as $facture) {
            try {
                Mail::to($facture->client_email)->send(new TicketEcheanceRetraitMail($facture));
                $envoyes++;
                $this->line("E-mail envoyé à {$facture->client_email} pour le ticket {$facture->num_ticket}");
            } catch (\Throwable $e) {
                Log::error("Erreur envoi e-mail échéance ticket {$facture->num_ticket}: ".$e->getMessage());
                $this->error("Échec pour {$facture->num_ticket}: ".$e->getMessage());
            }
        }

        $this->info("Terminé : {$envoyes} e-mail(s) d'échéance de retrait envoyé(s).");

        return self::SUCCESS;
    }
}
