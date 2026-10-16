<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('visites_techniques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicule_id')->constrained('vehicules')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->date('date_visite')->nullable();
            $table->date('date_echeance')->index();
            $table->date('date_assurance')->nullable()->index();
            $table->string('statut')->default('valide')->index(); // valide, expire, a_renouveler
            $table->string('centre_controle')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('derniere_notification_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visites_techniques');
    }
};
