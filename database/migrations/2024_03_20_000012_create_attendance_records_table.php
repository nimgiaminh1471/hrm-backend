<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('shift_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->dateTime('clock_in')->nullable();
            $table->dateTime('clock_out')->nullable();
            $table->integer('late_minutes')->default(0);
            $table->integer('early_leave_minutes')->default(0);
            $table->integer('overtime_minutes')->default(0);
            $table->integer('break_minutes')->default(0);
            $table->enum('status', ['present', 'absent', 'late', 'half_day', 'on_leave'])->default('absent');
            $table->string('clock_in_location')->nullable();
            $table->string('clock_out_location')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendance_records');
    }
};