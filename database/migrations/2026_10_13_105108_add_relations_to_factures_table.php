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
            $table->foreignId('client_id')->nullable()->after('user_id')->constrained('clients')->nullOnDelete();
            $table->foreignId('vehicule_id')->nullable()->after('client_id')->constrained('vehicules')->nullOnDelete();
            $table->foreignId('laveur_id')->nullable()->after('vehicule_id')->constrained('laveurs')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('factures', function (Blueprint $table) {
            $table->dropConstrainedForeignId('laveur_id');
            $table->dropConstrainedForeignId('vehicule_id');
            $table->dropConstrainedForeignId('client_id');
        });
    }
};
