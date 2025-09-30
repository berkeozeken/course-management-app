<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1) instructor_id kolonu yoksa ekle (nullable)
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'instructor_id')) {
                $table->foreignId('instructor_id')->nullable()->after('id');
            }
        });

        // 2) owner_id varsa, veriyi instructor_id'ye taşı + owner_id üstündeki FK'leri düşür + owner_id'yi kaldır
        if (Schema::hasColumn('courses', 'owner_id')) {
            // Veriyi taşı (instructor_id boşsa owner_id'yi yaz)
            DB::statement('UPDATE courses SET instructor_id = owner_id WHERE instructor_id IS NULL AND owner_id IS NOT NULL');

            // owner_id üzerine tanımlı tüm foreign key constraint'lerini bul ve sil
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
                // ÖNEMLİ: kaçışsız, IF EXISTS ile güvenli düşür
                DB::statement('ALTER TABLE courses DROP CONSTRAINT IF EXISTS ' . $r->constraint_name . ';');
            }

            // owner_id kolonunu kaldır (varsa)
            Schema::table('courses', function (Blueprint $table) {
                if (Schema::hasColumn('courses', 'owner_id')) {
                    try { $table->dropColumn('owner_id'); } catch (\Throwable $e) {}
                }
            });
        }

        // 3) instructor_id üzerinde eski FK varsa düşür ve doğru FK'yi ekle
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
                DB::statement('ALTER TABLE courses DROP CONSTRAINT IF EXISTS ' . $r->constraint_name . ';');
            }

            // Laravel schema ile FK ekle (NULL'da SET NULL, update'te CASCADE)
            Schema::table('courses', function (Blueprint $table) {
                $table->foreign('instructor_id')
                      ->references('id')->on('users')
                      ->nullOnDelete()
                      ->cascadeOnUpdate();
            });
        }
    }

    public function down(): void
    {
        // 1) owner_id yoksa geri ekle
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'owner_id')) {
                $table->foreignId('owner_id')->nullable()->after('id');
            }
        });

        // 2) instructor_id varsa, veriyi geri taşı (owner_id boşsa instructor_id yaz)
        if (Schema::hasColumn('courses', 'owner_id') && Schema::hasColumn('courses', 'instructor_id')) {
            DB::statement('UPDATE courses SET owner_id = instructor_id WHERE owner_id IS NULL AND instructor_id IS NOT NULL');
        }

        // 3) instructor_id üzerindeki FK'leri düşür ve kolonu kaldır
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
                DB::statement('ALTER TABLE courses DROP CONSTRAINT IF EXISTS ' . $r->constraint_name . ';');
            }

            Schema::table('courses', function (Blueprint $table) {
                $table->dropColumn('instructor_id');
            });
        }
    }
};
