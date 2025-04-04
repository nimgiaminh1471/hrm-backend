<?php

use App\Models\Department;
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
            ['key' => 'code', 'label' => 'Code'],
            ['key' => 'head.name', 'label' => 'Head'],
            ['key' => 'parent.name', 'label' => 'Parent Department'],
            ['key' => 'is_active', 'label' => 'Status']
        ];
    }

    public function departments()
    {
        $organization = auth()->user()->organization;

        return Department::where('organization_id', $organization->id)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('code', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy(...array_values($this->sortBy))
            ->paginate($this->perPage);
    }

    // Delete action
    public function delete($id): void
    {
        $this->warning('Department deleted', 'Department deleted successfully.', position: 'toast-bottom');
        $department = Department::find($id);
        $department->delete();
    }

    public function with(): array
    {
        return [
            'departments' => $this->departments(),
            'headers' => $this->headers()
        ];
    }

}
?>
<div>

    <x-header title="Departments" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Filters" @click="$wire.drawer = true" responsive icon="o-funnel" />
            <a href="{{ route('departments.create') }}"
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Create Department
            </a>
        </x-slot:actions>
    </x-header>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-4">
            <x-table :headers="$headers" :rows="$departments" striped with-pagination :sort-by="$sortBy"
                per-page="perPage" :per-page-values="[5, 10, 25, 50, 100]" link="departments/{id}">
                @scope('cell_name', $department)
                <div class="font-medium">{{ $department->name }}</div>
                <div class="text-sm text-gray-500">{{ $department->description }}</div>
                @endscope

                @scope('cell_head.name', $department)
                {{ $department->head?->name ?? 'Not assigned' }}
                @endscope

                @scope('cell_parent.name', $department)
                {{ $department->parent?->name ?? 'None' }}
                @endscope

                @scope('cell_is_active', $department)
                <x-badge :value="$department->is_active ? 'Active' : 'Inactive'" :type="$department->is_active ? 'success' : 'danger'" />
                @endscope

                @scope('actions', $department)
                <x-button icon="o-trash" wire:click="delete({{ $department['id'] }})" wire:confirm="Are you sure?"
                    spinner class="btn-ghost btn-sm text-error" />
                @endscope
            </x-table>
        </div>
    </div>
</div>