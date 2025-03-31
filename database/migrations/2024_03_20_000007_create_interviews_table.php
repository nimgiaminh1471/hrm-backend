<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('candidate_id')->constrained()->onDelete('cascade');
            $table->foreignId('job_posting_id')->constrained()->onDelete('cascade');
            $table->foreignId('interviewer_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['phone', 'video', 'in-person', 'technical', 'hr', 'final']);
            $table->dateTime('scheduled_at');
            $table->integer('duration_minutes');
            $table->string('location')->nullable();
            $table->string('meeting_link')->nullable();
            $table->enum('status', ['scheduled', 'completed', 'cancelled', 'rescheduled'])->default('scheduled');
            $table->json('interview_questions')->nullable();
            $table->json('feedback')->nullable();
            $table->decimal('rating', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('interviews');
    }
};