<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class UserManagement extends Component
{
    use WithPagination;

    public $search = '';

    public $perPage = 10;

    public $sortField = 'created_at';

    public $sortAsc = false;

    public $showTrashed = false;

    // For create/edit modal
    public $showCreateModal = false;

    public $showEditModal = false;

    public $userId;

    public $name;

    public $email;

    public $email_verified_at;

    public $password;

    public $password_confirmation;

    public $status;

    public $selectedRole = '';

    public array $selectedCompanies = [];

    // Delete confirmation
    public $showDeleteConfirmation = false;

    public $idToDelete = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
        'status' => 'required|in:active,inactive',
        'selectedRole' => 'nullable|exists:roles,name',
        'selectedCompanies' => 'nullable|array',
        'selectedCompanies.*' => 'exists:companies,id',
    ];

    protected $messages = [
        'email.unique' => 'The email has already been taken.',
        'password.confirmed' => 'The password confirmation does not match.',
    ];

    public function mount()
    {
        $this->status = 'active';
    }

    public function render()
    {
        $users = User::query()
            ->with(['roles', 'companies'])
            ->when($this->showTrashed, fn ($q) => $q->withTrashed())
            ->when(! $this->showTrashed, fn ($q) => $q->whereNull('deleted_at'))
            ->when($this->search, fn ($q) => $q->where('name', 'like', '%'.$this->search.'%')->orWhere('email', 'like', '%'.$this->search.'%'))
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);

        $roles = Role::orderBy('name')->get();
        $companies = \App\Models\Company::forUser()->where('status', 'active')->orderBy('name')->get();

        return view('livewire.user-management', compact('users', 'roles', 'companies'));
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
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'selectedRole' => 'nullable|exists:roles,name',
            'selectedCompanies' => 'nullable|array',
            'selectedCompanies.*' => 'exists:companies,id',
        ]);

        // Check if the user has permission to create users
        if (Gate::denies('create-user')) {
            session()->flash('error', 'You do not have permission to create users.');

            return;
        }

        if (! auth()->user()->canAccessAllCompanies()) {
            $allowed = auth()->user()->accessibleCompanyIds();
            foreach ($this->selectedCompanies as $cId) {
                if (! in_array((int) $cId, $allowed, true)) {
                    session()->flash('error', 'You cannot assign a company/workplace you do not have access to.');

                    return;
                }
            }
        }

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt($this->password),
            'email_verified_at' => now(),
        ]);

        if ($this->selectedRole) {
            $user->assignRole($this->selectedRole);
        }

        $user->companies()->sync($this->selectedCompanies);

        $this->showCreateModal = false;
        $this->resetInputFields();
        session()->flash('message', 'User created successfully.');
    }

    public function edit($id)
    {
        $user = User::withTrashed()->with('companies')->findOrFail($id);

        if (Gate::denies('edit-user')) {
            session()->flash('error', 'You do not have permission to edit users.');

            return;
        }

        $this->userId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->status = $user->trashed() ? 'inactive' : 'active';
        $this->selectedRole = $user->roles->first()?->name ?? '';
        $this->selectedCompanies = $user->companies->pluck('id')->map(fn ($id) => (string) $id)->toArray();

        $this->showEditModal = true;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$this->userId,
            'selectedRole' => 'nullable|exists:roles,name',
            'status' => 'required|in:active,inactive',
            'selectedCompanies' => 'nullable|array',
            'selectedCompanies.*' => 'exists:companies,id',
        ]);

        if (Gate::denies('edit-user')) {
            session()->flash('error', 'You do not have permission to edit users.');

            return;
        }

        if (! auth()->user()->canAccessAllCompanies()) {
            $allowed = auth()->user()->accessibleCompanyIds();
            foreach ($this->selectedCompanies as $cId) {
                if (! in_array((int) $cId, $allowed, true)) {
                    session()->flash('error', 'You cannot assign a company/workplace you do not have access to.');

                    return;
                }
            }
        }

        $user = User::withTrashed()->findOrFail($this->userId);
        $user->update(['name' => $this->name, 'email' => $this->email]);

        $this->selectedRole
            ? $user->syncRoles([$this->selectedRole])
            : $user->syncRoles([]);

        $user->companies()->sync($this->selectedCompanies);

        if ($this->status === 'inactive' && ! $user->trashed()) {
            $user->delete();
        } elseif ($this->status === 'active' && $user->trashed()) {
            $user->restore();
        }

        $this->showEditModal = false;
        $this->resetInputFields();
        session()->flash('message', 'User updated successfully.');
    }

    public function delete($id)
    {
        // Check if the user has permission to delete users
        if (Gate::denies('delete-user')) {
            session()->flash('error', 'You do not have permission to delete users.');

            return;
        }

        $this->idToDelete = $id;
        $this->showDeleteConfirmation = true;
    }

    public function confirmDelete()
    {
        if (! $this->idToDelete) {
            return;
        }

        $user = User::withTrashed()->findOrFail($this->idToDelete);
        $user->forceDelete();

        $this->showDeleteConfirmation = false;
        $this->idToDelete = null;

        session()->flash('message', 'User permanently deleted.');
    }

    public function restore($id)
    {
        // Check if the user has permission to restore users
        if (Gate::denies('restore-user')) {
            session()->flash('error', 'You do not have permission to restore users.');

            return;
        }

        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        session()->flash('message', 'User restored successfully.');
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->status = 'active';
        $this->selectedRole = '';
        $this->selectedCompanies = [];
    }
}

