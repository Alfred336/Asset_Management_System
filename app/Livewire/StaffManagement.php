<?php

namespace App\Livewire;

use App\Models\Company;
use App\Models\Staff;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class StaffManagement extends Component
{
    use WithPagination;

    public $search = '';

    public $showTrashed = false;

    public $perPage = 10;

    public $sortField = 'created_at';

    public $sortAsc = false;

    public $companyFilter = '';

    // For create/edit modal
    public $showCreateModal = false;

    public $showEditModal = false;

    public $staffId;

    public $company_id;

    public $first_name;

    public $last_name;

    public $email;

    public $phone;

    public $position;

    public $hire_date;

    public $salary;

    public $employment_type;

    public $status;

    public $notes;

    protected $rules = [
        'company_id' => 'required|exists:companies,id',
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:staff,email',
        'phone' => 'nullable|string|max:20',
        'position' => 'nullable|string|max:100',
        'hire_date' => 'nullable|date',
        'salary' => 'nullable|numeric|between:0,999999.99',
        'employment_type' => 'required|in:full_time,part_time,contract,intern',
        'status' => 'required|in:active,inactive,on_leave,terminated',
        'notes' => 'nullable|string',
    ];

    protected $messages = [
        'email.unique' => 'The email has already been taken.',
        'company_id.exists' => 'Please select a valid company.',
    ];

    public function mount()
    {
        $this->employment_type = 'full_time';
        $this->status = 'active';
    }

    public function render()
    {
        $companies = Company::where('status', 'active')->get();

        $staffQuery = Staff::with('company')
            ->where(function ($query) {
                $query->where('first_name', 'like', '%'.$this->search.'%')
                    ->orWhere('last_name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%')
                    ->orWhere('position', 'like', '%'.$this->search.'%');
            })
            ->when($this->companyFilter, function ($query) {
                $query->where('company_id', $this->companyFilter);
            })
            ->when(! $this->showTrashed, function ($query) {
                $query->whereNull('deleted_at');
            })
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc');

        $staff = $staffQuery->paginate($this->perPage);

        return view('livewire.staff-management', [
            'staff' => $staff,
            'companies' => $companies,
        ]);
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortAsc = ! $this->sortAsc;
        } else {
            $this->sortAsc = true;
        }

        $this->sortField = $field;
    }

    public function creating()
    {
        $this->resetValidation();
        $this->resetInputFields();
        $this->showCreateModal = true;
    }

    public function create()
    {
        $this->validate();

        // Check if the user has permission to create staff
        if (Gate::denies('create-staff')) {
            session()->flash('error', 'You do not have permission to create staff members.');

            return;
        }

        Staff::create([
            'company_id' => $this->company_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'position' => $this->position,
            'hire_date' => $this->hire_date,
            'salary' => $this->salary,
            'employment_type' => $this->employment_type,
            'status' => $this->status,
            'notes' => $this->notes,
        ]);

        $this->showCreateModal = false;
        $this->resetInputFields();
        session()->flash('message', 'Staff member created successfully.');
    }

    public function edit($id)
    {
        $staff = Staff::with('company')->findOrFail($id);

        // Check if the user has permission to edit staff
        if (Gate::denies('edit-staff')) {
            session()->flash('error', 'You do not have permission to edit staff members.');

            return;
        }

        $this->staffId = $id;
        $this->company_id = $staff->company_id;
        $this->first_name = $staff->first_name;
        $this->last_name = $staff->last_name;
        $this->email = $staff->email;
        $this->phone = $staff->phone;
        $this->position = $staff->position;
        $this->hire_date = $staff->hire_date;
        $this->salary = $staff->salary;
        $this->employment_type = $staff->employment_type;
        $this->status = $staff->status;
        $this->notes = $staff->notes;

        $this->showEditModal = true;
    }

    public function update()
    {
        $this->validate([
            'email' => 'required|email|max:255|unique:staff,email,'.$this->staffId,
        ]);

        // Check if the user has permission to edit staff
        if (Gate::denies('edit-staff')) {
            session()->flash('error', 'You do not have permission to edit staff members.');

            return;
        }

        $staff = Staff::findOrFail($this->staffId);
        $staff->update([
            'company_id' => $this->company_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'position' => $this->position,
            'hire_date' => $this->hire_date,
            'salary' => $this->salary,
            'employment_type' => $this->employment_type,
            'status' => $this->status,
            'notes' => $this->notes,
        ]);

        $this->showEditModal = false;
        $this->resetInputFields();
        session()->flash('message', 'Staff member updated successfully.');
    }

    public function delete($id)
    {
        // Check if the user has permission to delete staff
        if (Gate::denies('delete-staff')) {
            session()->flash('error', 'You do not have permission to delete staff members.');

            return;
        }

        $staff = Staff::findOrFail($id);
        $staff->delete();

        session()->flash('message', 'Staff member deleted successfully.');
    }

    public function resetInputFields()
    {
        $this->company_id = '';
        $this->first_name = '';
        $this->last_name = '';
        $this->email = '';
        $this->phone = '';
        $this->position = '';
        $this->hire_date = '';
        $this->salary = '';
        $this->employment_type = 'full_time';
        $this->status = 'active';
        $this->notes = '';
    }
}
