<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('segments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hackathon_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->longText('rules')->nullable();
            $table->longText('prizes')->nullable();
            $table->string('rulebook_path')->nullable();
            $table->unsignedSmallInteger('submission_limit')->nullable();
            $table->unsignedSmallInteger('max_teams')->nullable();
            $table->timestamp('registration_opens_at')->nullable();
            $table->timestamp('registration_closes_at')->nullable();
            $table->timestamp('submission_opens_at')->nullable();
            $table->timestamp('submission_closes_at')->nullable();
            $table->timestamp('results_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('cover_image')->nullable();
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('segments');
    }
};
