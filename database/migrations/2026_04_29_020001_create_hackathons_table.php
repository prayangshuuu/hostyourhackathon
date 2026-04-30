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
        Schema::create('hackathons', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('tagline')->nullable();
            $table->text('description');
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();
            $table->string('primary_color')->nullable()->default('#6366f1');
            $table->enum('status', ['draft', 'published', 'ongoing', 'ended', 'archived'])->default('draft');
            $table->boolean('allow_solo')->default(true);
            $table->unsignedTinyInteger('min_team_size')->default(1);
            $table->unsignedTinyInteger('max_team_size')->default(5);
            $table->timestamp('registration_opens_at')->nullable();
            $table->timestamp('registration_closes_at')->nullable();
            $table->timestamp('submission_opens_at')->nullable();
            $table->timestamp('submission_closes_at')->nullable();
            $table->timestamp('results_at')->nullable();
            $table->boolean('leaderboard_public')->default(false);
            $table->longText('rules')->nullable();
            $table->longText('prizes')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hackathons');
    }
};
