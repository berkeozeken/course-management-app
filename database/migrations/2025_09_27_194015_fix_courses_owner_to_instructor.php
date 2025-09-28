<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'instructor_id')) {
                $table->foreignId('instructor_id')->nullable()->after('id');
            }
        });

        if (Schema::hasColumn('courses', 'owner_id')) {
            DB::statement("UPDATE courses SET instructor_id = owner_id WHERE instructor_id IS NULL AND owner_id IS NOT NULL");

            $rows = DB::select("
                SELECT tc.constraint_name
                FROM information_schema.table_constraints tc
                JOIN information_schema.key_column_usage kcu
                  ON tc.constraint_name = kcu.constraint_name
                 AND tc.constraint_schema = kcu.constraint_schema
                WHERE tc.table_name = 'courses'
                  AND tc.constraint_type = 'FOREIGN KEY'
                  AND kcu.column_name = 'owner_id'
            ");
            foreach ($rows as $r) {
                DB::statement('ALTER TABLE courses DROP CONSTRAINT \"' . $r->constraint_name . '\"');
            }

            Schema::table('courses', function (Blueprint $table) {
                if (Schema::hasColumn('courses', 'owner_id')) {
                    try { $table->dropColumn('owner_id'); } catch (\Throwable $e) {}
                }
            });
        }

        if (Schema::hasColumn('courses', 'instructor_id')) {
            $rows = DB::select("
                SELECT tc.constraint_name
                FROM information_schema.table_constraints tc
                JOIN information_schema.key_column_usage kcu
                  ON tc.constraint_name = kcu.constraint_name
                 AND tc.constraint_schema = kcu.constraint_schema
                WHERE tc.table_name = 'courses'
                  AND tc.constraint_type = 'FOREIGN KEY'
                  AND kcu.column_name = 'instructor_id'
            ");
            foreach ($rows as $r) {
                DB::statement('ALTER TABLE courses DROP CONSTRAINT \"' . $r->constraint_name . '\"');
            }

            DB::statement('ALTER TABLE courses ADD CONSTRAINT courses_instructor_id_foreign FOREIGN KEY (\"instructor_id\") REFERENCES users(id) ON DELETE SET NULL');
        }
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'owner_id')) {
                $table->foreignId('owner_id')->nullable()->after('id');
            }
        });

        if (Schema::hasColumn('courses', 'owner_id') && Schema::hasColumn('courses', 'instructor_id')) {
            DB::statement("UPDATE courses SET owner_id = instructor_id WHERE owner_id IS NULL AND instructor_id IS NOT NULL");
        }

        if (Schema::hasColumn('courses', 'instructor_id')) {
            $rows = DB::select("
                SELECT tc.constraint_name
                FROM information_schema.table_constraints tc
                JOIN information_schema.key_column_usage kcu
                  ON tc.constraint_name = kcu.constraint_name
                 AND tc.constraint_schema = kcu.constraint_schema
                WHERE tc.table_name = 'courses'
                  AND tc.constraint_type = 'FOREIGN KEY'
                  AND kcu.column_name = 'instructor_id'
            ");
            foreach ($rows as $r) {
                DB::statement('ALTER TABLE courses DROP CONSTRAINT \"' . $r->constraint_name . '\"');
            }

            Schema::table('courses', function (Blueprint $table) {
                $table->dropColumn('instructor_id');
            });
        }
    }
};
