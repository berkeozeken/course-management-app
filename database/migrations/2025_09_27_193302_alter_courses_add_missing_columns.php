<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // instructor_id'yi ÖNCE nullable ekle
            if (!Schema::hasColumn('courses', 'instructor_id')) {
                $table->foreignId('instructor_id')
                      ->nullable()                 // <-- ÖNEMLİ
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

        // İstersen burada eski kayıtların instructor_id'sini bir kullanıcıya bağla:
        // Örn: ilk kullanıcıya set et (opsiyonel)
        // $firstUserId = DB::table('users')->min('id');
        // if ($firstUserId) {
        //     DB::table('courses')->whereNull('instructor_id')->update(['instructor_id' => $firstUserId]);
        // }

        // Sonrasında FK oluştur (nullable olduğu için sorun olmaz)
        Schema::table('courses', function (Blueprint $table) {
            // bazı DB'lerde yeniden çağırmak gerekir
            if (Schema::hasColumn('courses', 'instructor_id')) {
                $table->foreign('instructor_id')->references('id')->on('users')->nullOnDelete();
            }
        });

        // Eğer yukarıda backfill yaptıysan (hepsi doluysa) istersen NOT NULL'a çevirebilirsin:
        // Schema::table('courses', function (Blueprint $table) {
        //     $table->foreignId('instructor_id')->nullable(false)->change();
        // });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'instructor_id')) {
                $table->dropForeign(['instructor_id']);
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
