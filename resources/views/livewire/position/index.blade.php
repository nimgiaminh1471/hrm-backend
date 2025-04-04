<?php

use App\Models\Position;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new class extends Component {
    use WithPagination, Toast;

    public $search = '';
    public array $sortBy = ['column' => 'title', 'direction' => 'asc'];
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
            ['key' => 'title', 'label' => 'Title'],
            ['key' => 'code', 'label' => 'Code'],
            ['key' => 'description', 'label' => 'Description'],
            ['key' => 'base_salary', 'label' => 'Base Salary'],
            ['key' => 'level', 'label' => 'Level'],
            ['key' => 'is_active', 'label' => 'Status']
        ];
    }

    public function positions()
    {
        $organization = auth()->user()->organization;

        return Position::where('organization_id', $organization->id)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
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
        $this->warning('Position deleted', 'Position deleted successfully.', position: 'toast-bottom');
        $position = Position::find($id);
        $position->delete();
    }

    public function with(): array
    {
        return [
            'positions' => $this->positions(),
            'headers' => $this->headers()
        ];
    }
}
?>
<div>
    <x-header title="Positions" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Filters" @click="$wire.drawer = true" responsive icon="o-funnel" />
            <a href="{{ route('positions.create') }}"
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Create Position
            </a>
        </x-slot:actions>
    </x-header>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-4">
            <x-table :headers="$headers" :rows="$positions" striped with-pagination :sort-by="$sortBy"
                per-page="perPage" :per-page-values="[5, 10, 25, 50, 100]" link="positions/{id}">
                @scope('cell_title', $position)
                <div class="font-medium">{{ $position->title }}</div>
                <div class="text-sm text-gray-500">{{ $position->code }}</div>
                @endscope

                @scope('cell_department.name', $position)
                {{ $position->department?->name ?? 'Not assigned' }}
                @endscope

                @scope('cell_description', $position)
                {{ $position->description }}
                @endscope

                @scope('cell_is_active', $position)
                <x-badge :value="$position->is_active ? 'Active' : 'Inactive'" :type="$position->is_active ? 'success' : 'danger'" />
                @endscope

                @scope('actions', $position)
                <x-button icon="o-trash" wire:click="delete({{ $position['id'] }})" wire:confirm="Are you sure?" spinner class="btn-ghost btn-sm text-error" />
                @endscope
            </x-table>
        </div>
    </div>
</div> 