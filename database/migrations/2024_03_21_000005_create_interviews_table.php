<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('interviewer_id')->constrained('users')->cascadeOnDelete();
            $table->string('type'); // phone, video, in-person
            $table->string('status')->default('scheduled'); // scheduled, completed, cancelled
            $table->timestamp('scheduled_at');
            $table->integer('duration_minutes');
            $table->string('location')->nullable();
            $table->json('notes')->nullable();
            $table->json('feedback')->nullable();
            $table->integer('rating')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interviews');
    }
}; 