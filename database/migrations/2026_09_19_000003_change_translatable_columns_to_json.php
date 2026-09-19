<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Projects
        Schema::table('projects', function (Blueprint $table) {
            $table->text('title')->nullable()->change();
            $table->longText('summary')->nullable()->change();
            $table->text('role')->nullable()->change();
            $table->longText('description')->nullable()->change();
        });

        // 2. Profiles
        Schema::table('profiles', function (Blueprint $table) {
            $table->text('headline')->nullable()->change();
            $table->longText('tagline')->nullable()->change();
            $table->longText('bio')->nullable()->change();
            $table->longText('focus_areas')->nullable()->change();
            $table->text('location')->nullable()->change();
        });

        // 3. Experiences
        Schema::table('experiences', function (Blueprint $table) {
            $table->text('position')->nullable()->change();
            $table->text('employment_type')->nullable()->change();
            $table->text('location')->nullable()->change();
            $table->text('work_mode')->nullable()->change();
            $table->longText('description')->nullable()->change();
        });

        // 4. Educations
        Schema::table('educations', function (Blueprint $table) {
            $table->text('degree')->nullable()->change();
            $table->text('field')->nullable()->change();
            $table->text('location')->nullable()->change();
            $table->text('work_mode')->nullable()->change();
            $table->longText('description')->nullable()->change();
        });

        // 5. Skills
        Schema::table('skills', function (Blueprint $table) {
            $table->text('name')->nullable()->change();
        });
    }

    public function down(): void
    {
        // No-op
    }
};
