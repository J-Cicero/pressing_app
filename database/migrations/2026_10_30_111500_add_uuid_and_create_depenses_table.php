<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Tables requiring a public UUID identifier for non-predictable URLs.
     *
     * @var list<string>
     */
    private array $uuidTables = [
        'pressings',
        'users',
        'services',
        'clients',
        'vehicules',
        'laveurs',
        'regles_fidelite',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('role', 30)->default('caissier')->change();
        });

        foreach ($this->uuidTables as $tableName) {
            if (! Schema::hasColumn($tableName, 'uuid')) {
                Schema::table($tableName, function (Blueprint $table): void {
                    $table->uuid('uuid')->nullable()->after('id');
                });

                $rows = DB::table($tableName)->whereNull('uuid')->select('id')->get();
                foreach ($rows as $row) {
                    DB::table($tableName)
                        ->where('id', $row->id)
                        ->update(['uuid' => (string) Str::uuid()]);
                }

                Schema::table($tableName, function (Blueprint $table): void {
                    $table->unique('uuid');
                });
            }
        }

        if (! Schema::hasTable('depenses')) {
            Schema::create('depenses', function (Blueprint $table): void {
                $table->id();
                $table->uuid('uuid')->unique()->index();
                $table->unsignedBigInteger('pressing_id')->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('categorie', 100)->index();
                $table->string('libelle', 255);
                $table->decimal('montant', 12, 2);
                $table->date('date_depense')->index();
                $table->string('justificatif', 100)->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('depenses');

        foreach ($this->uuidTables as $tableName) {
            if (Schema::hasColumn($tableName, 'uuid')) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                    $table->dropUnique($tableName.'_uuid_unique');
                    $table->dropColumn('uuid');
                });
            }
        }
    }
};
