<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            if (!Schema::hasColumn('lessons', 'content')) {
                $table->text('content')->nullable()->after('title');
            }
            if (!Schema::hasColumn('lessons', 'video_url')) {
                $table->string('video_url')->nullable()->after('content');
            }
            if (!Schema::hasColumn('lessons', 'duration_minutes')) {
                $table->unsignedInteger('duration_minutes')->nullable()->after('video_url');
            }
            if (!Schema::hasColumn('lessons', 'is_free')) {
                $table->boolean('is_free')->default(false)->after('duration_minutes');
            }
            if (!Schema::hasColumn('lessons', 'position')) {
                $table->unsignedInteger('position')->default(1)->after('is_free');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            if (Schema::hasColumn('lessons', 'position')) $table->dropColumn('position');
            if (Schema::hasColumn('lessons', 'is_free')) $table->dropColumn('is_free');
            if (Schema::hasColumn('lessons', 'duration_minutes')) $table->dropColumn('duration_minutes');
            if (Schema::hasColumn('lessons', 'video_url')) $table->dropColumn('video_url');
            if (Schema::hasColumn('lessons', 'content')) $table->dropColumn('content');
        });
    }
};
