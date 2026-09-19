<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tablesColumns = [
            'projects' => ['title', 'summary', 'description', 'role'],
            'profiles' => ['headline', 'tagline', 'bio', 'focus_areas', 'location', 'languages'],
            'experiences' => ['position', 'employment_type', 'location', 'work_mode', 'description'],
            'educations' => ['degree', 'field', 'description'],
            'skills' => ['name'],
        ];

        foreach ($tablesColumns as $table => $columns) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            $fixedRows = 0;
            $rows = DB::table($table)->get();

            foreach ($rows as $row) {
                $updates = [];
                $rowChanged = false;

                foreach ($columns as $column) {
                    if (!isset($row->$column) || is_null($row->$column)) {
                        continue;
                    }

                    $rawVal = $row->$column;
                    $decoded = is_string($rawVal) ? json_decode($rawVal, true) : $rawVal;

                    if (is_array($decoded)) {
                        $hasId = isset($decoded['id']) && (!is_null($decoded['id']) && $decoded['id'] !== '');
                        $hasEn = isset($decoded['en']) && (!is_null($decoded['en']) && $decoded['en'] !== '');

                        // Jika "id" kosong dan "en" terisi -> pindahkan "en" ke "id" dan hapus "en"
                        if (!$hasId && $hasEn) {
                            $decoded['id'] = $decoded['en'];
                            unset($decoded['en']);
                            $updates[$column] = json_encode($decoded, JSON_UNESCAPED_UNICODE);
                            $rowChanged = true;
                        }
                    }
                }

                if ($rowChanged && !empty($updates)) {
                    DB::table($table)->where('id', $row->id)->update($updates);
                    $fixedRows++;
                }
            }

            echo "Fixed {$fixedRows} row(s) in table `{$table}`.\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reversing data fix is not strictly required, but no-op is safe
    }
};
