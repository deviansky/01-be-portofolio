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
        if (Schema::hasTable('profiles') && !Schema::hasColumn('profiles', 'languages')) {
            Schema::table('profiles', function (Blueprint $table) {
                $table->json('languages')->nullable()->after('location');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('profiles') && Schema::hasColumn('profiles', 'languages')) {
            Schema::table('profiles', function (Blueprint $table) {
                $table->dropColumn('languages');
            });
        }
    }
};
