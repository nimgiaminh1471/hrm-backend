<div class="department-node" style="margin-left: {{ $level * 2 }}rem">
    <div class="flex items-center space-x-2 py-2">
        @if ($department->children->isNotEmpty())
            <button
                type="button"
                wire:click="toggleDepartment({{ $department->id }})"
                class="text-gray-500 hover:text-gray-700"
            >
                <x-heroicon-o-chevron-down
                    class="w-5 h-5 transform transition-transform duration-200 {{ in_array($department->id, $this->expandedDepartments) ? '' : '-rotate-90' }}"
                />
            </button>
        @else
            <div class="w-5"></div>
        @endif

        <div class="flex-1">
            <div class="flex items-center justify-between">
                <div>
                    <span class="font-medium">{{ $department->name }}</span>
                    @if ($department->head)
                        <span class="text-sm text-gray-500">(Head: {{ $department->head->name }})</span>
                    @endif
                </div>
                <div class="flex items-center space-x-2">
                    <x-filament::button
                        wire:click="editDepartment({{ $department->id }})"
                        size="xs"
                    >
                        Edit
                    </x-filament::button>
                    <x-filament::button
                        wire:click="addSubDepartment({{ $department->id }})"
                        size="xs"
                    >
                        Add Sub-department
                    </x-filament::button>
                </div>
            </div>
            @if ($department->description)
                <p class="text-sm text-gray-500 mt-1">{{ $department->description }}</p>
            @endif
        </div>
    </div>

    @if (in_array($department->id, $this->expandedDepartments))
        <div class="ml-6 border-l-2 border-gray-200">
            @foreach ($department->children as $child)
                @include('filament.resources.department-resource.pages.partials.department-node', [
                    'department' => $child,
                    'level' => $level + 1
                ])
            @endforeach
        </div>
    @endif
</div> 