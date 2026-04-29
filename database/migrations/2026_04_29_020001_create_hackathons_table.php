<?php

use App\Enums\HackathonStatus;
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
            $table->string('tagline');
            $table->text('description');
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();
            $table->string('primary_color')->default('#6366f1');
            $table->string('status')->default(HackathonStatus::Draft->value);
            $table->boolean('allow_solo')->default(true);
            $table->unsignedSmallInteger('min_team_size')->default(1);
            $table->unsignedSmallInteger('max_team_size')->default(5);
            $table->timestamp('registration_opens_at')->nullable();
            $table->timestamp('registration_closes_at')->nullable();
            $table->timestamp('submission_opens_at')->nullable();
            $table->timestamp('submission_closes_at')->nullable();
            $table->timestamp('results_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
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
