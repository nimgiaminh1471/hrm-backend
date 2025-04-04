<?php

use App\Models\Department;
use App\Models\User;
use Livewire\Volt\Component;
use Illuminate\Validation\Rule;
use Mary\Traits\Toast;

new class extends Component {
    //
    use Toast;

    public Department $department;

    public string $name;

    public string $code;

    public string $description;

    public ?int $parent_id;

    public ?int $head_id;

    public bool $is_active;

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            // validate code to be unique except for the current department in organization
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('departments', 'code')->withoutTrashed()
                    ->where('organization_id', $this->department->organization_id)
                    ->ignore($this->department->id)
            ],
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:departments,id',
            'head_id' => 'nullable|exists:users,id',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function parentDepartments()
    {
        // Get all departments except the current department and allow null for parent_id
        $departments = Department::where('id', '!=', $this->department->id)->where('organization_id', auth()->user()->organization_id)->get();
        $departments->prepend(new Department(['id' => null, 'name' => 'None']));
        return $departments;
    }

    public function parentUsers()
    {
        // Get all users except the current user and allow null for head_id
        $users = User::where('organization_id', auth()->user()->organization_id)->get();
        $users->prepend(new User(['id' => null, 'name' => 'None']));
        return $users;
    }

    public function mount()
    {
        $this->name = $this->department->name;
        $this->code = $this->department->code;
        $this->description = $this->department->description;
        $this->parent_id = $this->department->parent_id;
        $this->head_id = $this->department->head_id;
        $this->is_active = $this->department->is_active;
    }

    public function back()
    {
        return redirect()->route('departments');
    }

    public function save()
    {
        $this->validate();
        $this->department->update($this->all());
        $this->success('Department updated', 'Department updated successfully.', position: 'toast-bottom');
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
    <x-header title="Edit Department" separator progress-indicator>
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
                <x-slot:actions>
                    <x-button label="Cancel" link="/users" />
                    {{-- The important thing here is `type="submit"` --}}
                    {{-- The spinner property is nice! --}}
                    <x-button label="Save" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
                </x-slot:actions>
            </x-form>
        </div>
    </div>
</div>