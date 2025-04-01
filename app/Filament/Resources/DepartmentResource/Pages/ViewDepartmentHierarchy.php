<?php

namespace App\Filament\Resources\DepartmentResource\Pages;

use App\Filament\Resources\DepartmentResource;
use App\Models\Department;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\View;
use Filament\Forms\Form;
use Illuminate\Support\Collection;

class ViewDepartmentHierarchy extends ViewRecord
{
    protected static string $resource = DepartmentResource::class;

    protected static string $view = 'filament.resources.department-resource.pages.view-department-hierarchy';

    public ?array $data = [];

    public array $expandedDepartments = [];

    public function mount(int|string $record): void
    {
        parent::mount($record);
        $this->expandedDepartments = $this->getRecord()->getAllParentIds()->toArray();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                View::make('filament.resources.department-resource.pages.department-hierarchy')
                    ->statePath('data'),
            ]);
    }

    public function toggleDepartment(int $departmentId): void
    {
        if (in_array($departmentId, $this->expandedDepartments)) {
            $this->expandedDepartments = array_diff($this->expandedDepartments, [$departmentId]);
        } else {
            $this->expandedDepartments[] = $departmentId;
        }
    }

    public function expandAll(): void
    {
        $this->expandedDepartments = $this->getRecord()->company->departments()->pluck('id')->toArray();
    }

    public function collapseAll(): void
    {
        $this->expandedDepartments = [];
    }

    public function editDepartment(int $departmentId): void
    {
        $this->redirect(route('filament.admin.resources.departments.edit', $departmentId));
    }

    public function addSubDepartment(int $parentId): void
    {
        $this->redirect(route('filament.admin.resources.departments.create', [
            'parent_id' => $parentId,
            'company_id' => $this->getRecord()->company_id,
        ]));
    }
} 