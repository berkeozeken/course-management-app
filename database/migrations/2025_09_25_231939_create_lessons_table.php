<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete();
            $table->string('title');
            $table->string('video_url')->nullable();
            $table->longText('content')->nullable();
            $table->unsignedInteger('position')->default(1);
            $table->boolean('is_free')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('lessons'); }
};
