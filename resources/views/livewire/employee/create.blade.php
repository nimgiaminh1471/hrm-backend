<?php

use App\Models\User;
use App\Models\Department;
use App\Models\Position;
use App\Models\Team;
use App\Enums\Gender;
use App\Enums\MaritalStatus;
use App\Enums\EmploymentStatus;
use App\Enums\ContractType;
use Livewire\Volt\Component;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;

    // Personal Information
    public $first_name = '';
    public $last_name = '';
    public $email = '';
    public $employee_id = '';
    public $gender = '';
    public $date_of_birth = '';
    public $marital_status = '';
    public $nationality = '';
    public $national_id = '';
    public $passport_number = '';
    public $passport_expiry = '';

    // Contact Information
    public $phone = '';
    public $address = '';
    public $city = '';
    public $state = '';
    public $country = '';
    public $postal_code = '';
    public $phone_emergency = '';
    public $address_emergency = '';
    public $emergency_contact_name = '';
    public $emergency_contact_phone = '';
    public $emergency_contact_relationship = '';

    // Employment Information
    public $department_id = null;
    public $position_id = null;
    public $team_id = null;
    public $manager_id = null;
    public $hire_date = '';
    public $joining_date = '';
    public $exit_date = '';
    public $employment_status = '';
    public $employment_type = '';
    public $salary = 0;

    // Bank Information
    public $bank_name = '';
    public $bank_account = '';
    public $bank_branch = '';
    public $tax_id = '';
    public $social_security_number = '';

    // Additional Information
    public $skills = [];
    public $certifications = [];
    public $education = [];
    public $experience = [];
    public $is_active = true;

    public function rules()
    {
        return [
            // Personal Information
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'employee_id' => 'required|string|unique:users,employee_id',
            'gender' => 'required|string|in:' . implode(',', Gender::values()),
            'date_of_birth' => 'required|date',
            'marital_status' => 'required|string|in:' . implode(',', MaritalStatus::values()),
            'nationality' => 'required|string|max:255',
            'national_id' => 'nullable|string|max:255',
            'passport_number' => 'nullable|string|max:255',
            'passport_expiry' => 'nullable|date',

            // Contact Information
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'postal_code' => 'required|string|max:255',
            'phone_emergency' => 'required|string|max:255',
            'address_emergency' => 'required|string|max:255',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:255',
            'emergency_contact_relationship' => 'required|string|max:255',

            // Employment Information
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'team_id' => 'nullable|exists:teams,id',
            'manager_id' => 'nullable|exists:users,id',
            'hire_date' => 'required|date',
            'joining_date' => 'required|date',
            'exit_date' => 'nullable|date',
            'employment_status' => 'required|string|in:' . implode(',', EmploymentStatus::values()),
            'employment_type' => 'required|string|in:' . implode(',', ContractType::values()),
            'salary' => 'required|numeric|min:0',

            // Bank Information
            'bank_name' => 'required|string|max:255',
            'bank_account' => 'required|string|max:255',
            'bank_branch' => 'required|string|max:255',
            'tax_id' => 'nullable|string|max:255',
            'social_security_number' => 'nullable|string|max:255',

            // Additional Information
            'skills' => 'nullable|array',
            'certifications' => 'nullable|array',
            'education' => 'nullable|array',
            'experience' => 'nullable|array',
            'is_active' => 'boolean',
        ];
    }

    public function save()
    {
        $data = $this->validate();

        $user = User::create([
            ...$data,
            'organization_id' => auth()->user()->organization_id,
            'password' => bcrypt('password'), // Default password, should be changed on first login
            'name' => $data['first_name'] . ' ' . $data['last_name'],
        ]);

        $this->success('Employee created successfully.', redirectTo: route('employees.index'));
    }

    public function with(): array
    {
        $organization = auth()->user()->organization;

        return [
            'departments' => Department::where('organization_id', $organization->id)->get(),
            'positions' => Position::where('organization_id', $organization->id)->get(),
            'teams' => Team::where('organization_id', $organization->id)->get(),
            'managers' => User::where('organization_id', $organization->id)
                ->where('id', '!=', auth()->id())
                ->get(),
            'genders' => Gender::options(),
            'maritalStatuses' => MaritalStatus::options(),
            'employmentStatuses' => EmploymentStatus::options(),
            'contractTypes' => ContractType::options(),
        ];
    }
}
?>
<div>
    <x-header title="Add New Employee" separator progress-indicator>
        <x-slot:actions>
            <x-button label="Cancel" link="{{ route('employees.index') }}" />
        </x-slot:actions>
    </x-header>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-4">
            <form wire:submit="save">
                <div class="space-y-6">
                    <!-- Personal Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <h3 class="col-span-2 text-lg font-semibold">Personal Information</h3>
                        <x-input label="First Name" wire:model="first_name" />
                        <x-input label="Last Name" wire:model="last_name" />
                        <x-input label="Email" wire:model="email" type="email" />
                        <x-input label="Employee ID" wire:model="employee_id" />
                        <x-select label="Gender" wire:model="gender" :options="$genders" />
                        <x-input label="Date of Birth" wire:model="date_of_birth" type="date" />
                        <x-select label="Marital Status" wire:model="marital_status" :options="$maritalStatuses" />
                        <x-input label="Nationality" wire:model="nationality" />
                        <x-input label="National ID" wire:model="national_id" />
                        <x-input label="Passport Number" wire:model="passport_number" />
                        <x-input label="Passport Expiry" wire:model="passport_expiry" type="date" />
                    </div>

                    <!-- Contact Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <h3 class="col-span-2 text-lg font-semibold">Contact Information</h3>
                        <x-input label="Phone" wire:model="phone" />
                        <x-input label="Address" wire:model="address" />
                        <x-input label="City" wire:model="city" />
                        <x-input label="State" wire:model="state" />
                        <x-input label="Country" wire:model="country" />
                        <x-input label="Postal Code" wire:model="postal_code" />
                        <x-input label="Emergency Contact Name" wire:model="emergency_contact_name" />
                        <x-input label="Emergency Contact Phone" wire:model="emergency_contact_phone" />
                        <x-input label="Emergency Contact Relationship" wire:model="emergency_contact_relationship" />
                        <x-input label="Emergency Address" wire:model="address_emergency" />
                        <x-input label="Emergency Phone" wire:model="phone_emergency" />
                    </div>

                    <!-- Employment Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <h3 class="col-span-2 text-lg font-semibold">Employment Information</h3>
                        <x-select
                            label="Department"
                            wire:model="department_id"
                            :options="$departments"
                            option-label="name"
                            option-value="id"
                            placeholder="Select a department"
                        />
                        <x-select
                            label="Position"
                            wire:model="position_id"
                            :options="$positions"
                            option-label="name"
                            option-value="id"
                            placeholder="Select a position"
                        />
                        <x-select
                            label="Team"
                            wire:model="team_id"
                            :options="$teams"
                            option-label="name"
                            option-value="id"
                            placeholder="Select a team"
                        />
                        <x-select
                            label="Manager"
                            wire:model="manager_id"
                            :options="$managers"
                            option-label="name"
                            option-value="id"
                            placeholder="Select a manager"
                        />
                        <x-input label="Hire Date" wire:model="hire_date" type="date" />
                        <x-input label="Joining Date" wire:model="joining_date" type="date" />
                        <x-input label="Exit Date" wire:model="exit_date" type="date" />
                        <x-select label="Employment Status" wire:model="employment_status" :options="$employmentStatuses" />
                        <x-select label="Employment Type" wire:model="employment_type" :options="$contractTypes" />
                        <x-input label="Salary" wire:model="salary" type="number" />
                    </div>

                    <!-- Bank Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <h3 class="col-span-2 text-lg font-semibold">Bank Information</h3>
                        <x-input label="Bank Name" wire:model="bank_name" />
                        <x-input label="Bank Account" wire:model="bank_account" />
                        <x-input label="Bank Branch" wire:model="bank_branch" />
                        <x-input label="Tax ID" wire:model="tax_id" />
                        <x-input label="Social Security Number" wire:model="social_security_number" />
                    </div>

                    <!-- Additional Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <h3 class="col-span-2 text-lg font-semibold">Additional Information</h3>
                        <x-checkbox label="Active" wire:model="is_active" />
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <x-button label="Save" type="submit" class="btn-primary" />
                </div>
            </form>
        </div>
    </div>
</div> 