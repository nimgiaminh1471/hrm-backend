<?php

use App\Models\Position;
use App\Models\Department;
use Livewire\Volt\Component;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;

    public $name = '';
    public $code = '';
    public $description = '';
    public $department_id = null;
    public $is_active = true;

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:positions,code',
            'description' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'is_active' => 'boolean',
        ];
    }

    public function save()
    {
        $data = $this->validate();

        Position::create([
            ...$data,
            'organization_id' => auth()->user()->organization_id,
        ]);

        $this->success('Position created successfully.', redirectTo: route('positions'));
    }

    public function with(): array
    {
        $organization = auth()->user()->organization;

        return [
            'departments' => Department::where('organization_id', $organization->id)
                ->where('is_active', true)
                ->get(),
        ];
    }
}
?>
<div>
    <x-header title="Add New Position" separator progress-indicator>
        <x-slot:actions>
            <x-button label="Cancel" link="{{ route('positions') }}" />
        </x-slot:actions>
    </x-header>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-4">
            <form wire:submit="save">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-input label="Name" wire:model="name" />
                    <x-input label="Code" wire:model="code" />
                    
                    <x-select
                        label="Department"
                        wire:model="department_id"
                        :options="$departments"
                        option-label="name"
                        option-value="id"
                        placeholder="Select a department"
                    />
                    
                    <x-textarea label="Description" wire:model="description" />
                    
                    <x-checkbox label="Active" wire:model="is_active" />
                </div>

                <div class="mt-4 flex justify-end">
                    <x-button label="Save" type="submit" class="btn-primary" />
                </div>
            </form>
        </div>
    </div>
</div> 