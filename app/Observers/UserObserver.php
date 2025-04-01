<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Employee;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        if ($user->company_id) {
            Employee::create([
                'company_id' => $user->company_id,
                'user_id' => $user->id,
                'first_name' => $user->name,
                'last_name' => '', // Can be updated later
                'email' => $user->email,
                'is_active' => true,
                'joining_date' => now(),
            ]);
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Update employee records if user details changed
        if ($user->wasChanged(['name', 'email'])) {
            $employees = Employee::where('user_id', $user->id)->get();

            foreach ($employees as $employee) {
                $employee->update([
                    'first_name' => $user->name,
                    'email' => $user->email,
                ]);
            }
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        // Soft delete associated employee records
        Employee::where('user_id', $user->id)->delete();
    }
}