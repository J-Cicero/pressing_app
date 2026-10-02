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
        Schema::table('factures', function (Blueprint $table) {
            $table->string('client_nom')->nullable()->after('user_id');
            $table->string('client_telephone')->nullable()->index()->after('client_nom');
            $table->date('date_retrait_prevue')->nullable()->after('statut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('factures', function (Blueprint $table) {
            $table->dropColumn(['client_nom', 'client_telephone', 'date_retrait_prevue']);
        });
    }
};
