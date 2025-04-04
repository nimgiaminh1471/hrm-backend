<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('departments', 'department.index')
    ->name('departments');

Volt::route('departments/create', 'department.create')
    ->name('departments.create');

Volt::route('departments/{department}', 'department.edit')
    ->name('departments.edit');

Volt::route('employees', 'employee.index')
    ->name('employees');

Volt::route('employees/create', 'employee.create')
    ->name('employees.create');

Volt::route('employees/{employee}', 'employee.edit')
    ->name('employees.edit');

Volt::route('positions', 'position.index')
    ->name('positions');

Volt::route('positions/create', 'position.create')
    ->name('positions.create');

Volt::route('positions/{position}', 'position.edit')
    ->name('positions.edit');

Volt::route('contracts', 'contract.index')
    ->name('contracts');

Volt::route('contracts/create', 'contract.create')
    ->name('contracts.create');

Volt::route('contracts/{contract}', 'contract.edit')
    ->name('contracts.edit');
