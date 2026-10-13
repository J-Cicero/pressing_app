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
        Schema::create('laveurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pressing_id')->constrained('pressings')->cascadeOnDelete();
            $table->string('nom');
            $table->string('telephone')->nullable();
            $table->string('type_remuneration')->default('pourcentage'); // pourcentage, forfait
            $table->decimal('valeur_remuneration', 10, 2)->default(0.00);
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laveurs');
    }
};
