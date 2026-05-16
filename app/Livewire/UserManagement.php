<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

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

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
        'status' => 'required:in:active,inactive',
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
            ->when($this->showTrashed, function ($query) {
                $query->withTrashed();
            })
            ->when(! $this->showTrashed, function ($query) {
                $query->whereNull('deleted_at');
            })
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            })
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);

        return view('livewire.user-management', [
            'users' => $users,
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
        $this->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Check if the user has permission to create users
        if (Gate::denies('create-user')) {
            session()->flash('error', 'You do not have permission to create users.');

            return;
        }

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt($this->password),
            'email_verified_at' => now(),
        ]);

        $this->showCreateModal = false;
        $this->resetInputFields();
        session()->flash('message', 'User created successfully.');
    }

    public function edit($id)
    {
        $user = User::withTrashed()->findOrFail($id);

        // Check if the user has permission to edit users
        if (Gate::denies('edit-user')) {
            session()->flash('error', 'You do not have permission to edit users.');

            return;
        }

        $this->userId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->status = $user->trashed() ? 'inactive' : 'active';

        $this->showEditModal = true;
    }

    public function update()
    {
        $this->validate([
            'email' => 'required|email|max:255|unique:users,email,'.$this->userId,
        ]);

        // Check if the user has permission to edit users
        if (Gate::denies('edit-user')) {
            session()->flash('error', 'You do not have permission to edit users.');

            return;
        }

        $user = User::withTrashed()->findOrFail($this->userId);
        $user->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        // Handle status change (soft delete/restore)
        if ($this->status === 'inactive' && ! $user->trashed()) {
            $user->delete();
        } elseif ($this->status === 'active' && $user->trashed()) {
            $user->restore();
        }

        $this->showEditModal = false;
        $this->resetInputFields();
        session()->flash('message', 'User updated successfully.');
    }

    public function forceDelete($id)
    {
        // Check if the user has permission to delete users
        if (Gate::denies('delete-user')) {
            session()->flash('error', 'You do not have permission to delete users.');

            return;
        }

        $user = User::withTrashed()->findOrFail($id);
        $user->forceDelete();

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
    }
}
