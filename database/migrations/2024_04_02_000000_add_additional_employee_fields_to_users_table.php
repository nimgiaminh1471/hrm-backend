<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Basic Information
            $table->string('first_name')->after('name');
            $table->string('last_name')->after('first_name');
            $table->string('phone')->after('date_of_birth');

            // Address Information
            $table->text('address')->after('phone');
            $table->string('city')->after('address');
            $table->string('state')->after('city');
            $table->string('country')->after('state');
            $table->string('postal_code')->after('country');

            // Emergency Contact
            $table->string('emergency_contact_name')->nullable()->after('address_emergency');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            $table->string('emergency_contact_relationship')->nullable()->after('emergency_contact_phone');

            // Employment Information
            $table->string('employment_type')->after('employment_status');
            $table->decimal('salary', 10, 2)->after('employment_type');
            $table->string('bank_name')->after('salary');
            $table->string('bank_account')->after('bank_name');
            $table->string('bank_branch')->after('bank_account');
            $table->string('tax_id')->after('bank_branch');
            $table->string('social_security_number')->after('tax_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Basic Information
            $table->dropColumn([
                'first_name',
                'last_name',
                'phone',
            ]);

            // Address Information
            $table->dropColumn([
                'address',
                'city',
                'state',
                'country',
                'postal_code',
            ]);

            // Emergency Contact
            $table->dropColumn([
                'emergency_contact_name',
                'emergency_contact_phone',
                'emergency_contact_relationship',
            ]);

            // Employment Information
            $table->dropColumn([
                'employment_type',
                'salary',
                'bank_name',
                'bank_account',
                'bank_branch',
                'tax_id',
                'social_security_number',
            ]);
        });
    }
}; 