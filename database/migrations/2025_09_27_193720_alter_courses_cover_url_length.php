<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('courses', 'cover_url')) {
            DB::statement('ALTER TABLE courses ALTER COLUMN cover_url TYPE varchar(2048)');
        }
    }
    public function down(): void
    {
        if (Schema::hasColumn('courses', 'cover_url')) {
            DB::statement('ALTER TABLE courses ALTER COLUMN cover_url TYPE varchar(255)');
        }
    }
};
