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
        Schema::create('paiements_laveurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laveur_id')->constrained('laveurs')->cascadeOnDelete();
            $table->string('mois', 7); // Format: YYYY-MM
            $table->decimal('montant_total', 10, 2)->default(0);
            $table->decimal('montant_paye', 10, 2)->default(0);
            $table->enum('statut', ['en_attente', 'paye', 'partiel'])->default('en_attente');
            $table->string('mode_paiement')->default('especes');
            $table->date('date_paiement')->nullable();
            $table->foreignId('paye_par_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->unique(['laveur_id', 'mois']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiements_laveurs');
    }
};
