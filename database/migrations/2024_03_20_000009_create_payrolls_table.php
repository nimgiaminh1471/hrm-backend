<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('payroll_period_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->decimal('basic_salary', 10, 2);
            $table->decimal('gross_salary', 10, 2);
            $table->decimal('net_salary', 10, 2);
            $table->json('allowances')->nullable();
            $table->json('deductions')->nullable();
            $table->decimal('overtime_amount', 10, 2)->default(0);
            $table->decimal('bonus_amount', 10, 2)->default(0);
            $table->decimal('commission_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2);
            $table->decimal('insurance_amount', 10, 2);
            $table->decimal('loan_amount', 10, 2)->default(0);
            $table->decimal('leave_deduction', 10, 2)->default(0);
            $table->json('other_earnings')->nullable();
            $table->json('other_deductions')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'processing', 'completed', 'cancelled'])->default('draft');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payrolls');
    }
};