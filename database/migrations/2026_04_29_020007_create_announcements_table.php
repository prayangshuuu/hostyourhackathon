<?php

use App\Enums\AnnouncementVisibility;
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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hackathon_id')->constrained('hackathons')->cascadeOnDelete();
            $table->string('title');
            $table->text('body');
            $table->string('visibility')->default(AnnouncementVisibility::All->value);
            $table->foreignId('segment_id')->nullable()->constrained('segments')->nullOnDelete();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('published_at')->nullable();
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
        Schema::dropIfExists('announcements');
    }
};
