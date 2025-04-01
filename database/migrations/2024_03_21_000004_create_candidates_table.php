<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_posting_id')->constrained()->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('resume_path')->nullable();
            $table->text('cover_letter')->nullable();
            $table->float('experience_years')->nullable();
            $table->decimal('current_salary', 10, 2)->nullable();
            $table->decimal('expected_salary', 10, 2)->nullable();
            $table->integer('notice_period')->nullable();
            $table->string('status')->default('applied'); // applied, screening, interviewed, offered, hired, rejected
            $table->json('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
}; 