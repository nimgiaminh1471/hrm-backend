<?php

use App\Models\Contract;
use App\Models\User;
use App\Models\Position;
use App\Enums\ContractType;
use App\Enums\ContractStatus;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Illuminate\Validation\Rule;


new class extends Component {
    use Toast;

    public Contract $contract;

    // Contract Information
    public $contract_number = '';
    public $user_id = null;
    public $position_id = null;
    public $type = '';
    public $start_date = '';
    public $end_date = '';
    public $salary = 0;
    public $benefits = '';
    public $terms_and_conditions = '';
    public $status = '';

    public function mount(Contract $contract)
    {
        $this->contract = $contract;
        $this->contract_number = $contract->contract_number;
        $this->user_id = $contract->user_id;
        $this->position_id = $contract->position_id;
        $this->type = $contract->type->value;
        $this->start_date = $contract->start_date?->format('Y-m-d');
        $this->end_date = $contract->end_date?->format('Y-m-d');
        $this->salary = $contract->salary;
        $this->benefits = $contract->benefits;
        $this->terms_and_conditions = $contract->terms_and_conditions;
        $this->status = $contract->status->value;
    }

    public function rules()
    {
        return [
            'contract_number' => 'required|string|unique:contracts,contract_number,' . $this->contract->id,
            'user_id' => ['required', 'exists:users,id'],
            'position_id' => ['required', 'exists:positions,id'],
            'type' => ['required', 'string', Rule::in(ContractType::values())],
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'salary' => 'required|numeric|min:0',
            'benefits' => 'nullable|string',
            'terms_and_conditions' => 'nullable|string',
            'status' => ['required', 'string', Rule::in(ContractStatus::values())],
        ];
    }

    public function save()
    {
        $data = $this->validate();

        $this->contract->update($data);

        $this->success('Contract updated successfully.', redirectTo: route('contracts'));
    }

    public function with(): array
    {
        $organization = auth()->user()->organization;

        return [
            'users' => User::where('organization_id', $organization->id)->get(),
            'positions' => Position::where('organization_id', $organization->id)->get(),
            'contractTypes' => ContractType::options(),
            'contractStatuses' => ContractStatus::options(),
        ];
    }
}
?>
<div>
    <x-header title="Edit Contract" separator progress-indicator>
        <x-slot:actions>
            <x-button label="Cancel" link="{{ route('contracts') }}" />
        </x-slot:actions>
    </x-header>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-4">
            <form wire:submit="save">
                <div class="space-y-6">
                    <!-- Contract Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <h3 class="col-span-2 text-lg font-semibold">Contract Information</h3>
                        <x-input label="Contract Number" wire:model="contract_number" />
                        <x-select
                            label="Employee"
                            wire:model="user_id"
                            :options="$users"
                            option-label="name"
                            option-value="id"
                            placeholder="Select an employee"
                        />
                        <x-select
                            label="Position"
                            wire:model="position_id"
                            :options="$positions"
                            option-label="name"
                            option-value="id"
                            placeholder="Select a position"
                        />
                        <x-select label="Contract Type" wire:model="type" :options="$contractTypes" />
                        <x-input label="Start Date" wire:model="start_date" type="date" />
                        <x-input label="End Date" wire:model="end_date" type="date" />
                        <x-input label="Salary" wire:model="salary" type="number" />
                        <x-select label="Status" wire:model="status" :options="$contractStatuses" />
                    </div>

                    <!-- Additional Information -->
                    <div class="grid grid-cols-1 gap-4">
                        <h3 class="text-lg font-semibold">Additional Information</h3>
                        <x-textarea label="Benefits" wire:model="benefits" rows="3" />
                        <x-textarea label="Terms and Conditions" wire:model="terms_and_conditions" rows="5" />
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <x-button label="Save" type="submit" class="btn-primary" />
                </div>
            </form>
        </div>
    </div>
</div> 