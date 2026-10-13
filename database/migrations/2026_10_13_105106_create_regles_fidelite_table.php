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
        Schema::create('regles_fidelite', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('seuil_visites')->index();
            $table->string('type_avantage'); // pourcentage, montant_fixe, cadeau, service_offert
            $table->decimal('valeur_avantage', 10, 2)->nullable();
            $table->string('description');
            $table->string('type_activite')->default('global'); // pressing, lavage, global
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regles_fidelite');
    }
};
