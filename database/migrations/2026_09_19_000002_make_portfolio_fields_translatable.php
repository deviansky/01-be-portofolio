<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Projects
        $projects = DB::table('projects')->get();
        foreach ($projects as $row) {
            $updates = [];
            foreach (['title', 'summary', 'role', 'description'] as $field) {
                if (isset($row->$field) && $row->$field !== null) {
                    $val = $row->$field;
                    $decoded = json_decode($val, true);
                    if (!is_array($decoded) || !isset($decoded['id'])) {
                        $updates[$field] = json_encode(['id' => $val], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                }
            }
            if ($updates) {
                DB::table('projects')->where('id', $row->id)->update($updates);
            }
        }
        Schema::table('projects', function (Blueprint $table) {
            $table->json('title')->change();
            $table->json('summary')->change();
            $table->json('role')->nullable()->change();
            $table->json('description')->nullable()->change();
        });

        // 2. Profiles
        $profiles = DB::table('profiles')->get();
        foreach ($profiles as $row) {
            $updates = [];
            foreach (['headline', 'tagline', 'bio', 'focus_areas', 'location'] as $field) {
                if (isset($row->$field) && $row->$field !== null) {
                    $val = $row->$field;
                    $decoded = json_decode($val, true);
                    if (!is_array($decoded) || !isset($decoded['id'])) {
                        $actualVal = is_array($decoded) ? $decoded : $val;
                        $updates[$field] = json_encode(['id' => $actualVal], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                }
            }
            if ($updates) {
                DB::table('profiles')->where('id', $row->id)->update($updates);
            }
        }
        Schema::table('profiles', function (Blueprint $table) {
            $table->json('headline')->change();
            $table->json('tagline')->change();
            $table->json('bio')->change();
            $table->json('focus_areas')->change();
            $table->json('location')->change();
        });

        // 3. Experiences
        $experiences = DB::table('experiences')->get();
        foreach ($experiences as $row) {
            $updates = [];
            foreach (['position', 'employment_type', 'location', 'work_mode', 'description'] as $field) {
                if (isset($row->$field) && $row->$field !== null) {
                    $val = $row->$field;
                    $decoded = json_decode($val, true);
                    if (!is_array($decoded) || !isset($decoded['id'])) {
                        $updates[$field] = json_encode(['id' => $val], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                }
            }
            if ($updates) {
                DB::table('experiences')->where('id', $row->id)->update($updates);
            }
        }
        Schema::table('experiences', function (Blueprint $table) {
            $table->json('position')->change();
            $table->json('employment_type')->nullable()->change();
            $table->json('location')->nullable()->change();
            $table->json('work_mode')->nullable()->change();
            $table->json('description')->nullable()->change();
        });

        // 4. Educations
        $educations = DB::table('educations')->get();
        foreach ($educations as $row) {
            $updates = [];
            foreach (['degree', 'field', 'description'] as $field) {
                if (isset($row->$field) && $row->$field !== null) {
                    $val = $row->$field;
                    $decoded = json_decode($val, true);
                    if (!is_array($decoded) || !isset($decoded['id'])) {
                        $updates[$field] = json_encode(['id' => $val], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                }
            }
            if ($updates) {
                DB::table('educations')->where('id', $row->id)->update($updates);
            }
        }
        Schema::table('educations', function (Blueprint $table) {
            $table->json('degree')->change();
            $table->json('field')->change();
            $table->json('description')->nullable()->change();
        });

        // 5. Skills
        $skills = DB::table('skills')->get();
        foreach ($skills as $row) {
            $updates = [];
            foreach (['name'] as $field) {
                if (isset($row->$field) && $row->$field !== null) {
                    $val = $row->$field;
                    $decoded = json_decode($val, true);
                    if (!is_array($decoded) || !isset($decoded['id'])) {
                        $updates[$field] = json_encode(['id' => $val], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                }
            }
            if ($updates) {
                DB::table('skills')->where('id', $row->id)->update($updates);
            }
        }
        Schema::table('skills', function (Blueprint $table) {
            $table->json('name')->change();
        });
    }

    public function down(): void
    {
        $tables = [
            'projects' => ['title' => 'string', 'summary' => 'text', 'role' => 'string', 'description' => 'text'],
            'profiles' => ['headline' => 'string', 'tagline' => 'text', 'bio' => 'text', 'focus_areas' => 'text', 'location' => 'string'],
            'experiences' => ['position' => 'string', 'employment_type' => 'string', 'location' => 'string', 'work_mode' => 'string', 'description' => 'text'],
            'educations' => ['degree' => 'string', 'field' => 'string', 'description' => 'text'],
            'skills' => ['name' => 'string'],
        ];

        foreach ($tables as $tableName => $fields) {
            $rows = DB::table($tableName)->get();
            foreach ($rows as $row) {
                $updates = [];
                foreach (array_keys($fields) as $field) {
                    if (isset($row->$field) && $row->$field !== null) {
                        $decoded = json_decode($row->$field, true);
                        if (is_array($decoded) && isset($decoded['id'])) {
                            $idVal = $decoded['id'];
                            $updates[$field] = is_array($idVal) ? json_encode($idVal, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $idVal;
                        }
                    }
                }
                if ($updates) {
                    DB::table($tableName)->where('id', $row->id)->update($updates);
                }
            }
        }
    }
};
