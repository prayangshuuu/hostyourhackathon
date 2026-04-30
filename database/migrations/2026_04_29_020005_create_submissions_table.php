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
            $table->foreignId('segment_id')->nullable()->constrained('segments')->nullOnDelete();
            $table->string('title');
            $table->text('problem_statement');
            $table->longText('description');
            $table->text('tech_stack')->nullable();
            $table->string('demo_url')->nullable();
            $table->string('repo_url')->nullable();
            $table->boolean('is_draft')->default(true);
            $table->timestamp('submitted_at')->nullable();
            $table->boolean('re_open_submission')->default(false);
            $table->boolean('disqualified')->default(false);
            $table->string('disqualified_reason')->nullable();
            $table->foreignId('disqualified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('disqualified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('hackathon_id');
            $table->index('team_id');
            $table->index('is_draft');
            $table->index('disqualified');
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
