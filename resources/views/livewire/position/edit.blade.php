<?php

use App\Models\Position;
use App\Models\Department;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use App\Enums\PositionLevel;
use Illuminate\Validation\Rule;

new class extends Component {
    use Toast;

    public Position $position;
    public $title = '';
    public $code = '';
    public $description = '';
    public $responsibilities = '';
    public $requirements = '';
    public $base_salary = '';
    public $level = '';
    public $is_active = true;

    public function mount(Position $position)
    {
        $this->position = $position;
        $this->title = $position->title;
        $this->code = $position->code;
        $this->description = $position->description;
        $this->responsibilities = $position->responsibilities;
        $this->requirements = $position->requirements;
        $this->base_salary = $position->base_salary;
        $this->level = $position->level;
        $this->is_active = $position->is_active;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('positions', 'code')->where('organization_id', auth()->user()->organization_id)->withoutTrashed()->ignore($this->position->id)
            ],
            'description' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'requirements' => 'nullable|string',
            'base_salary' => 'required|numeric|min:0',
            'level' => 'required|string|in:' . implode(',', PositionLevel::values()),
            'is_active' => 'boolean',
        ];
    }

    public function back()
    {
        return redirect()->route('positions');
    }

    public function save()
    {
        $data = $this->validate();

        $this->position->update($data);

        $this->success('Position updated successfully.', redirectTo: route('positions'));
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
    <x-header title="Edit Position" separator progress-indicator>
        <x-slot:actions>
            <x-button label="Back" @click="$wire.back()" responsive icon="o-arrow-left" />
        </x-slot:actions>
    </x-header>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-4">
            <form wire:submit="save">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-input label="Title" wire:model="title" />
                    <x-input label="Code" wire:model="code" />

                    <x-textarea label="Description" wire:model="description" />
                    <x-textarea label="Responsibilities" wire:model="responsibilities" />
                    <x-textarea label="Requirements" wire:model="requirements" />
                    <x-input label="Base Salary" wire:model="base_salary" />
                    <x-select label="Level" wire:model="level" :options="PositionLevel::options()" />

                    <x-checkbox label="Active" wire:model="is_active" />
                </div>

                <div class="mt-4 flex justify-end">
                    <x-button label="Save" type="submit" class="btn-primary" />
                </div>
            </form>
        </div>
    </div>
</div>