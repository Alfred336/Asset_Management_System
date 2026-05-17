<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleManagement extends Component
{
    use WithPagination;

    public $search = '';

    public $perPage = 10;

    public $sortField = 'name';

    public $sortAsc = true;

    public $showCreateModal = false;

    public $showEditModal = false;

    public $roleId;

    public $name;

    public $selectedPermissions = [];

    protected $rules = [
        'name' => 'required|string|max:255|unique:roles,name',
    ];

    public function render()
    {
        $roles = Role::with('permissions')
            ->where('name', 'like', '%'.$this->search.'%')
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);

        $permissions = Permission::all();

        return view('livewire.role-management', [
            'roles' => $roles,
            'permissions' => $permissions,
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

        if (Gate::denies('edit-roles')) {
            session()->flash('error', 'You do not have permission to manage roles.');

            return;
        }

        $role = Role::create(['name' => $this->name]);
        $role->syncPermissions($this->selectedPermissions);

        $this->showCreateModal = false;
        $this->resetInputFields();
        session()->flash('message', 'Role created successfully.');
    }

    public function edit($id)
    {
        if (Gate::denies('edit-roles')) {
            session()->flash('error', 'You do not have permission to manage roles.');

            return;
        }

        $role = Role::findOrFail($id);
        $this->roleId = $id;
        $this->name = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();

        $this->showEditModal = true;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:roles,name,'.$this->roleId,
        ]);

        if (Gate::denies('edit-roles')) {
            session()->flash('error', 'You do not have permission to manage roles.');

            return;
        }

        $role = Role::findOrFail($this->roleId);
        $role->update(['name' => $this->name]);
        $role->syncPermissions($this->selectedPermissions);

        $this->showEditModal = false;
        $this->resetInputFields();
        session()->flash('message', 'Role updated successfully.');
    }

    public function delete($id)
    {
        if (Gate::denies('edit-roles')) {
            session()->flash('error', 'You do not have permission to manage roles.');

            return;
        }

        $role = Role::findOrFail($id);

        if ($role->name === 'Super Admin') {
            session()->flash('error', 'The Super Admin role cannot be deleted.');

            return;
        }

        $role->delete();
        session()->flash('message', 'Role deleted successfully.');
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->selectedPermissions = [];
        $this->roleId = null;
    }
}
