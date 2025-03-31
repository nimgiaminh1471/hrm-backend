<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('job_posting_id')->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('resume_path');
            $table->string('cover_letter_path')->nullable();
            $table->json('skills')->nullable();
            $table->json('experience')->nullable();
            $table->json('education')->nullable();
            $table->enum('status', [
                'new',
                'reviewing',
                'shortlisted',
                'interview_scheduled',
                'interviewed',
                'offered',
                'hired',
                'rejected',
                'withdrawn'
            ])->default('new');
            $table->text('notes')->nullable();
            $table->json('interview_feedback')->nullable();
            $table->decimal('interview_score', 5, 2)->nullable();
            $table->date('interview_date')->nullable();
            $table->json('offer_details')->nullable();
            $table->date('offer_date')->nullable();
            $table->date('offer_deadline')->nullable();
            $table->enum('offer_status', ['pending', 'accepted', 'rejected', 'expired'])->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('candidates');
    }
};