<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->foreignId('position_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->text('requirements');
            $table->text('responsibilities');
            $table->json('qualifications');
            $table->decimal('salary_min', 10, 2)->nullable();
            $table->decimal('salary_max', 10, 2)->nullable();
            $table->string('location');
            $table->enum('employment_type', ['full-time', 'part-time', 'contract', 'internship']);
            $table->enum('experience_level', ['entry', 'mid', 'senior', 'lead', 'manager']);
            $table->date('application_deadline');
            $table->json('benefits')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->json('posting_channels')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_postings');
    }
};