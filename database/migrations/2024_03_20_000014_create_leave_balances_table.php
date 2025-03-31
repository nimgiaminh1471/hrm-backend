<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('leave_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('leave_type_id')->constrained()->onDelete('cascade');
            $table->integer('year');
            $table->decimal('total_days', 5, 2);
            $table->decimal('used_days', 5, 2)->default(0);
            $table->decimal('carried_forward_days', 5, 2)->default(0);
            $table->decimal('remaining_days', 5, 2);
            $table->json('accrual_history')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('leave_balances');
    }
};