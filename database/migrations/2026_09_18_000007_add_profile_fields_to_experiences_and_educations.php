<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->string('work_mode')->nullable()->after('location'); // Di lokasi, Hibrida, Jarak jauh
            $table->string('logo_url')->nullable()->after('work_mode');
            $table->json('skills')->nullable()->after('highlights');
        });

        Schema::table('educations', function (Blueprint $table) {
            $table->string('location')->nullable()->after('field');
            $table->string('work_mode')->nullable()->after('location');
            $table->string('logo_url')->nullable()->after('work_mode');
            $table->json('skills')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->dropColumn(['work_mode', 'logo_url', 'skills']);
        });

        Schema::table('educations', function (Blueprint $table) {
            $table->dropColumn(['location', 'work_mode', 'logo_url', 'skills']);
        });
    }
};
