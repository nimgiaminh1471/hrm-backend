<?php

use App\Models\User;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new class extends Component {
    use WithPagination, Toast;

    public $search = '';
    public array $sortBy = ['column' => 'name', 'direction' => 'asc'];
    public $perPage = 10;

    // Clear filters
    public function clear(): void
    {
        $this->reset();
        $this->success('Filters cleared.', position: 'toast-bottom');
    }

    public function headers(): array
    {
        return [
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'employee_id', 'label' => 'Employee ID'],
            ['key' => 'department.name', 'label' => 'Department'],
            ['key' => 'position.name', 'label' => 'Position'],
            ['key' => 'manager.name', 'label' => 'Manager'],
            ['key' => 'employment_status', 'label' => 'Status']
        ];
    }

    public function employees()
    {
        $organization = auth()->user()->organization;

        return User::where('organization_id', $organization->id)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('employee_id', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->with(['department', 'position', 'manager'])
            ->orderBy(...array_values($this->sortBy))
            ->paginate($this->perPage);
    }

    // Delete action
    public function delete($id): void
    {
        $this->warning('Employee deleted', 'Employee deleted successfully.', position: 'toast-bottom');
        $user = User::find($id);
        $user->delete();
    }

    public function with(): array
    {
        return [
            'employees' => $this->employees(),
            'headers' => $this->headers()
        ];
    }
}
?>
<div>
    <x-header title="Employees" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Filters" @click="$wire.drawer = true" responsive icon="o-funnel" />
            <a href="{{ route('employees.create') }}"
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Add Employee
            </a>
        </x-slot:actions>
    </x-header>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-4">
            <x-table :headers="$headers" :rows="$employees" striped with-pagination :sort-by="$sortBy"
                per-page="perPage" :per-page-values="[5, 10, 25, 50, 100]" link="employees/{id}">
                @scope('cell_name', $employee)
                <div class="font-medium">{{ $employee->name }}</div>
                <div class="text-sm text-gray-500">{{ $employee->email }}</div>
                @endscope

                @scope('cell_department.name', $employee)
                {{ $employee->department?->name ?? 'Not assigned' }}
                @endscope

                @scope('cell_position.name', $employee)
                {{ $employee->position?->name ?? 'Not assigned' }}
                @endscope

                @scope('cell_manager.name', $employee)
                {{ $employee->manager?->name ?? 'Not assigned' }}
                @endscope

                @scope('cell_employment_status', $employee)
                <x-badge :value="$employee->employment_status->value" :type="$employee->employment_status === \App\Enums\EmploymentStatus::ACTIVE ? 'success' : 'warning'" />
                @endscope

                @scope('actions', $employee)
                <x-button icon="o-trash" wire:click="delete({{ $employee['id'] }})" wire:confirm="Are you sure?" spinner class="btn-ghost btn-sm text-error" />
                @endscope
            </x-table>
        </div>
    </div>
</div> 