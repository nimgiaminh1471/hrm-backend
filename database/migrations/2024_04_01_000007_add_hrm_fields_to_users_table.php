<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('team_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('position_id')->nullable()->constrained()->onDelete('set null');
            $table->string('employee_id')->nullable()->unique();
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('nationality')->nullable();
            $table->string('national_id')->nullable();
            $table->string('passport_number')->nullable();
            $table->date('passport_expiry')->nullable();
            $table->string('phone_emergency')->nullable();
            $table->string('address_emergency')->nullable();
            $table->date('joining_date')->nullable();
            $table->date('exit_date')->nullable();
            $table->string('employment_status')->nullable(); // active, on_leave, terminated, etc.
            $table->text('skills')->nullable();
            $table->text('certifications')->nullable();
            $table->text('education')->nullable();
            $table->text('experience')->nullable();
            $table->string('profile_photo')->nullable();
            $table->boolean('is_active')->default(true);
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropForeign(['department_id']);
            $table->dropForeign(['team_id']);
            $table->dropForeign(['position_id']);
            $table->dropColumn([
                'organization_id',
                'department_id',
                'team_id',
                'position_id',
                'employee_id',
                'date_of_birth',
                'gender',
                'marital_status',
                'nationality',
                'national_id',
                'passport_number',
                'passport_expiry',
                'phone_emergency',
                'address_emergency',
                'joining_date',
                'exit_date',
                'employment_status',
                'skills',
                'certifications',
                'education',
                'experience',
                'profile_photo',
                'is_active'
            ]);
        });
    }
}; 