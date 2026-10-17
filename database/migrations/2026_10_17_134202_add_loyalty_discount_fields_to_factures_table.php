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
            $table->decimal('montant_brut', 10, 2)->nullable()->after('client_email');
            $table->decimal('remise_fidelite', 10, 2)->default(0.00)->after('montant_brut');
            $table->foreignId('regle_fidelite_id')->nullable()->after('remise_fidelite')->constrained('regles_fidelite')->nullOnDelete();
            $table->foreignId('client_fidelite_avantage_id')->nullable()->after('regle_fidelite_id')->constrained('client_fidelite_avantages')->nullOnDelete();
            $table->string('details_remise')->nullable()->after('client_fidelite_avantage_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('factures', function (Blueprint $table) {
            $table->dropForeign(['regle_fidelite_id']);
            $table->dropForeign(['client_fidelite_avantage_id']);
            $table->dropColumn([
                'montant_brut',
                'remise_fidelite',
                'regle_fidelite_id',
                'client_fidelite_avantage_id',
                'details_remise',
            ]);
        });
    }
};
