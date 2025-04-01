<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->foreignId('position_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->json('requirements');
            $table->json('responsibilities');
            $table->json('qualifications');
            $table->float('experience_years')->nullable();
            $table->decimal('salary_min', 10, 2)->nullable();
            $table->decimal('salary_max', 10, 2)->nullable();
            $table->string('salary_type')->nullable(); // hourly, monthly, yearly
            $table->string('job_type'); // full-time, part-time, contract, internship
            $table->string('location')->nullable();
            $table->string('remote_type')->nullable(); // remote, hybrid, on-site
            $table->string('status')->default('draft'); // draft, published, closed
            $table->timestamp('published_at')->nullable();
            $table->timestamp('closing_date')->nullable();
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_postings');
    }
}; 