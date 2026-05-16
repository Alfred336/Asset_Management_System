<?php

namespace App\Livewire;

use App\Models\Company;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class CompanyManagement extends Component
{
    use WithPagination;

    public $search = '';

    public $showTrashed = false;

    public $perPage = 10;

    public $sortField = 'created_at';

    public $sortAsc = false;

    // For create/edit modal
    public $showCreateModal = false;

    public $showEditModal = false;

    public $companyId;

    public $name;

    public $email;

    public $phone;

    public $website;

    public $address;

    public $tax_id;

    public $status;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:companies,email',
        'phone' => 'nullable|string|max:20',
        'website' => 'nullable|url|max:255',
        'address' => 'nullable|string',
        'tax_id' => 'nullable|string|max:50',
        'status' => 'required|in:active,inactive',
    ];

    protected $messages = [
        'email.unique' => 'The email has already been taken.',
    ];

    public function mount()
    {
        $this->status = 'active';
    }

    public function render()
    {
        $companies = Company::where(function ($query) {
            $query->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('email', 'like', '%'.$this->search.'%');
        })
            ->when(! $this->showTrashed, function ($query) {
                $query->whereNull('deleted_at');
            })
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);

        return view('livewire.company-management', [
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

        // Check if the user has permission to create companies
        if (Gate::denies('create-companies')) {
            session()->flash('error', 'You do not have permission to create companies.');

            return;
        }

        Company::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'website' => $this->website,
            'address' => $this->address,
            'tax_id' => $this->tax_id,
            'status' => $this->status,
        ]);

        $this->showCreateModal = false;
        $this->resetInputFields();
        session()->flash('message', 'Company created successfully.');
    }

    public function edit($id)
    {
        $company = Company::findOrFail($id);

        // Check if the user has permission to edit companies
        if (Gate::denies('edit-companies')) {
            session()->flash('error', 'You do not have permission to edit companies.');

            return;
        }

        $this->companyId = $id;
        $this->name = $company->name;
        $this->email = $company->email;
        $this->phone = $company->phone;
        $this->website = $company->website;
        $this->address = $company->address;
        $this->tax_id = $company->tax_id;
        $this->status = $company->status;

        $this->showEditModal = true;
    }

    public function update()
    {
        $this->validate([
            'email' => 'required|email|max:255|unique:companies,email,'.$this->companyId,
        ]);

        // Check if the user has permission to edit companies
        if (Gate::denies('edit-companies')) {
            session()->flash('error', 'You do not have permission to edit companies.');

            return;
        }

        $company = Company::findOrFail($this->companyId);
        $company->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'website' => $this->website,
            'address' => $this->address,
            'tax_id' => $this->tax_id,
            'status' => $this->status,
        ]);

        $this->showEditModal = false;
        $this->resetInputFields();
        session()->flash('message', 'Company updated successfully.');
    }

    public function delete($id)
    {
        // Check if the user has permission to delete companies
        if (Gate::denies('delete-companies')) {
            session()->flash('error', 'You do not have permission to delete companies.');

            return;
        }

        $company = Company::findOrFail($id);
        $company->delete();

        session()->flash('message', 'Company deleted successfully.');
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->website = '';
        $this->address = '';
        $this->tax_id = '';
        $this->status = 'active';
    }
}
