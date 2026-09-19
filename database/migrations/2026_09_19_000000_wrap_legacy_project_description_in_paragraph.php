<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $projects = DB::table('projects')->whereNotNull('description')->get();

        foreach ($projects as $project) {
            $desc = trim((string) $project->description);
            if ($desc !== '' && !str_starts_with($desc, '<')) {
                $escaped = e($desc);
                DB::table('projects')->where('id', $project->id)->update([
                    'description' => '<p>' . $escaped . '</p>',
                ]);
            }
        }
    }

    public function down(): void
    {
        // No revert necessary
    }
};
