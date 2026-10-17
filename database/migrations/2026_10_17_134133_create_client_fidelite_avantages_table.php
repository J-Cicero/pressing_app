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
        Schema::create('client_fidelite_avantages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('regle_fidelite_id')->constrained('regles_fidelite')->cascadeOnDelete();
            $table->string('statut')->default('disponible'); // disponible, utilise, expire
            $table->unsignedSmallInteger('utilisations_restantes')->default(1);
            $table->timestamp('date_expiration')->nullable()->index();
            $table->timestamp('debloque_at')->useCurrent();
            $table->timestamp('last_used_at')->nullable();
            $table->foreignId('facture_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_fidelite_avantages');
    }
};
