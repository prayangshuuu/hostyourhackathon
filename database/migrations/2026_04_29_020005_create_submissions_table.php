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
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('hackathon_id')->constrained('hackathons')->cascadeOnDelete();
            $table->string('title');
            $table->text('problem_statement');
            $table->text('description');
            $table->text('tech_stack');
            $table->string('demo_url')->nullable();
            $table->string('repo_url')->nullable();
            $table->boolean('is_draft')->default(true);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
