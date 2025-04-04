<?php

use App\Models\Contract;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Livewire\WithPagination;
new class extends Component {
    use WithPagination, Toast;

    public $search = '';
    public array $sortBy = ['column' => 'contract_number', 'direction' => 'asc'];
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
            ['key' => 'contract_number', 'label' => 'Contract Number'],
            ['key' => 'user.name', 'label' => 'Employee'],
            ['key' => 'position.title', 'label' => 'Position'],
            ['key' => 'type', 'label' => 'Type'],
            ['key' => 'start_date', 'label' => 'Start Date'],
            ['key' => 'end_date', 'label' => 'End Date'],
            ['key' => 'salary', 'label' => 'Salary'],
        ];
    }

    public function contracts()
    {
        $organization = auth()->user()->organization;

        return Contract::where('organization_id', $organization->id)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('contract_number', 'like', '%' . $this->search . '%')
                        ->orWhere('type', 'like', '%' . $this->search . '%')
                        ->orWhere('status', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy(...array_values($this->sortBy))
            ->paginate($this->perPage);
    }

    public function with(): array
    {
        $organization = auth()->user()->organization;

        return [
            'headers' => $this->headers(),
            'contracts' => $this->contracts(),
        ];
    }
}
?>
<div>
    <x-header title="Contracts" separator progress-indicator>
        <x-slot:actions>
            <x-button label="Add Contract" link="{{ route('contracts.create') }}" class="btn-primary" />
        </x-slot:actions>
    </x-header>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-4">
            <x-table :headers="$headers" :rows="$contracts" with-pagination>
                @scope('cell_contract_number', $contract)
                    <a href="{{ route('contracts.edit', $contract) }}" class="text-primary hover:underline">
                        {{ $contract->contract_number }}
                    </a>
                @endscope

                @scope('cell_type', $contract)
                    <x-badge :value="$contract->type->label($contract->type)" />
                @endscope

                @scope('cell_start_date', $contract)
                    {{ $contract->start_date?->format('Y-m-d') }}
                @endscope

                @scope('cell_end_date', $contract)
                    {{ $contract->end_date?->format('Y-m-d') }}
                @endscope

                @scope('cell_salary', $contract)
                    {{ number_format($contract->salary, 2) }}
                @endscope

                @scope('cell_status', $contract)
                    <x-badge :value="$contract->status->label()" :class="$contract->status->color()" />
                @endscope

                @scope('actions', $contract)
                    <div class="flex items-center gap-2">
                        <x-button icon="o-pencil" class="btn-ghost btn-sm" link="{{ route('contracts.edit', $contract) }}" />
                        <x-button icon="o-trash" class="btn-ghost btn-sm text-red-500" wire:click="delete({{ $contract->id }})" wire:confirm="Are you sure you want to delete this contract?" />
                    </div>
                @endscope
            </x-table>
        </div>
    </div>
</div> 