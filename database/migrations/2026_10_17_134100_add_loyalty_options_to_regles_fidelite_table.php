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
        Schema::table('regles_fidelite', function (Blueprint $table) {
            $table->unsignedSmallInteger('nombre_utilisations')->default(1)->after('valeur_avantage');
            $table->unsignedSmallInteger('duree_jours')->nullable()->after('nombre_utilisations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('regles_fidelite', function (Blueprint $table) {
            $table->dropColumn(['nombre_utilisations', 'duree_jours']);
        });
    }
};
