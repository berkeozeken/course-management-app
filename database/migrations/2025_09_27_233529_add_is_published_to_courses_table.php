<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'is_published')) {
                // cover_url yoksa ->after('description') yap
                $after = Schema::hasColumn('courses','cover_url') ? 'cover_url' : 'description';
                $table->boolean('is_published')->default(false)->after($after);
            }
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'is_published')) {
                $table->dropColumn('is_published');
            }
        });
    }
};
