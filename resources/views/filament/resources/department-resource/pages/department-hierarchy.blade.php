<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-medium">Department Hierarchy</h2>
        <div class="flex items-center space-x-2">
            <x-filament::button
                wire:click="expandAll"
                size="sm"
            >
                Expand All
            </x-filament::button>
            <x-filament::button
                wire:click="collapseAll"
                size="sm"
            >
                Collapse All
            </x-filament::button>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="p-4">
            @php
                $departments = $this->getRecord()->company->departments()->whereNull('parent_id')->get();
            @endphp

            @foreach ($departments as $department)
                <div class="department-tree">
                    @include('filament.resources.department-resource.pages.partials.department-node', [
                        'department' => $department,
                        'level' => 0
                    ])
                </div>
            @endforeach
        </div>
    </div>
</div> 