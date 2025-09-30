<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1) Kolonları güvenle ekle
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'instructor_id')) {
                $table->foreignId('instructor_id')
                      ->nullable()
                      ->after('id');
            }

            if (!Schema::hasColumn('courses', 'title')) {
                $table->string('title')->after('instructor_id');
            }
            if (!Schema::hasColumn('courses', 'slug')) {
                $table->string('slug')->unique()->after('title');
            }
            if (!Schema::hasColumn('courses', 'description')) {
                $table->text('description')->nullable()->after('slug');
            }
            if (!Schema::hasColumn('courses', 'price')) {
                $table->decimal('price', 10, 2)->nullable()->after('description');
            }
            if (!Schema::hasColumn('courses', 'cover_url')) {
                $table->string('cover_url')->nullable()->after('price');
            }
            if (!Schema::hasColumn('courses', 'is_published')) {
                $table->boolean('is_published')->default(false)->after('cover_url');
            }
            if (!Schema::hasColumn('courses', 'created_at')) {
                $table->timestamps();
            }
        });

        // 2) FK'yi çakışmadan ekle (PostgreSQL için mevcutsa önce düşür)
        if (Schema::hasColumn('courses', 'instructor_id')) {
            // Bazı ortamlarda aynı isimli constraint zaten var olabiliyor
            DB::statement('ALTER TABLE "courses" DROP CONSTRAINT IF EXISTS "courses_instructor_id_foreign";');

            Schema::table('courses', function (Blueprint $table) {
                // FK'yi yeniden tanımla (nullable olduğundan NULL silmede sorun olmaz)
                $table->foreign('instructor_id')
                      ->references('id')->on('users')
                      ->nullOnDelete()
                      ->cascadeOnUpdate();
            });
        }
    }

    public function down(): void
    {
        // FK'yi güvenle kaldır
        DB::statement('ALTER TABLE "courses" DROP CONSTRAINT IF EXISTS "courses_instructor_id_foreign";');

        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'instructor_id')) {
                $table->dropColumn('instructor_id');
            }

            foreach (['title','slug','description','price','cover_url','is_published'] as $col) {
                if (Schema::hasColumn('courses', $col)) {
                    $table->dropColumn($col);
                }
            }

            if (Schema::hasColumn('courses', 'created_at')) {
                $table->dropTimestamps();
            }
        });
    }
};
