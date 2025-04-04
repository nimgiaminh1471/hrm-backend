<?php

use App\Models\Department;
use App\Models\User;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Illuminate\Validation\Rule;

new class extends Component {
    //
    use Toast;

    public string $name;

    public string $code;

    public string $description;

    public ?int $parent_id;

    public ?int $head_id;

    public bool $is_active = true;

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('departments', 'code')
                    ->withoutTrashed()
                    ->where('organization_id', auth()->user()->organization_id)
            ],
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:departments,id',
            'head_id' => 'nullable|exists:users,id',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function back()
    {
        return redirect()->route('departments');
    }

    public function save()
    {
        $this->validate();
        $data = $this->all();
        $data['organization_id'] = auth()->user()->organization_id;
        Department::create($data);
        $this->success('Department created', 'Department created successfully.', position: 'toast-bottom');
        return redirect()->route('departments');
    }

    public function parentDepartments()
    {
        $departments = Department::where('organization_id', auth()->user()->organization_id)->get();
        $departments->prepend(new Department(['id' => null, 'name' => 'None']));
        return $departments;
    }

    public function parentUsers()
    {
        $users = User::where('organization_id', auth()->user()->organization_id)->get();
        $users->prepend(new User(['id' => null, 'name' => 'None']));
        return $users;
    }

    public function with(): array
    {
        return [
            'parentDepartments' => $this->parentDepartments(),
            'parentUsers' => $this->parentUsers(),
        ];
    }

}; ?>

<div>
    <x-header title="Create Department" separator progress-indicator>
        <x-slot:actions>
            <x-button label="Back" @click="$wire.back()" responsive icon="o-arrow-left" />
        </x-slot:actions>
    </x-header>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-4">
            <x-form wire:submit="save">
                <x-input label="Name" wire:model="name" />
                <x-input label="Code" wire:model="code" />
                <x-input label="Description" wire:model="description" />
                <x-select label="Parent Department" wire:model="parent_id" :options="$parentDepartments" />
                <x-select label="Head of Department" wire:model="head_id" :options="$parentUsers" />
                <x-toggle label="Status" wire:model="is_active" />
                <x-button label="Save" type="submit" />
            </x-form>
        </div>
    </div>
</div>