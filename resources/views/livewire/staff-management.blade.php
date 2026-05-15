<div>
    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- Search, Filter and Create Button -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex-1 md:md:flex-row md:justify-between">
            <div class="w-full md:w-auto">
                <input type="text"
                       wire:model.debounce.500ms="search"
                       placeholder="Search staff members..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mt-4 md:mt-0 md:w-auto">
                <select wire:model="companyFilter"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Companies</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ $companyFilter == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <button wire:click="creating"
                    class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    @can('create-staff')
                    >
                Add Staff Member
            </button>
        </div>
    </div>

    <!-- Sortable Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 text-left text-sm font-medium">
                        <button wire:click="sortBy('first_name')"
                                class="flex items-center">
                            Name
                            <svg class="ml-1 h-4 w-4" @if($sortField === 'first_name') @if($sortAsc) fill="currentColor" @endif @endif xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 1 0 111.414 0L10 10.586l3.293-3.293a1 1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                        </button>
                    </th>
                    <th class="px-4 py-2 text-left text-sm font-medium">
                        <button wire:click="sortBy('email')"
                                class="flex items-center">
                            Email
                            <svg class="ml-1 h-4 w-4" @if($sortField === 'email') @if($sortAsc) fill="currentColor" @endif @endif xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 1 0 111.414 0L10 10.586l3.293-3.293a1 1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                        </button>
                    </th>
                    <th class="px-4 py-2 text-left text-sm font-medium">Company</th>
                    <th class="px-4 py-2 text-left text-sm font-medium">Position</th>
                    <th class="px-4 py-2 text-left text-sm font-medium">Status</th>
                    <th class="px-4 py-2 text-left text-sm font-medium">Actions</th>
                </tr>
            </thead>
            <tbody>
                @if($staff->isEmpty())
                    <tr>
                        <td colspan="6" class="px-4 py-2 text-center text-gray-500">
                            No staff members found.
                        </td>
                    </tr>
                @else
                    @foreach($staff as $member)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $member->full_name }}</td>
                            <td class="px-4 py-2">{{ $member->email }}</td>
                            <td class="px-4 py-2">{{ $member->company->name ?? 'N/A' }}</td>
                            <td class="px-4 py-2">{{ $member->position ?? 'N/A' }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($member->status === 'active') bg-green-100 text-green-800
                                        @elseif($member->status === 'inactive') bg-gray-100 text-gray-800
                                        @elseif($member->status === 'on_leave') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $member->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 space-x-2">
                                <!-- Edit Button -->
                                <button wire:click="edit({{ $member->id }})"
                                        class="px-3 py-1 bg-yellow-500 text-white text-sm rounded-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2"
                                        @can('edit-staff')
                                        >
                                    Edit
                                </button>
                                <!-- Delete Button -->
                                <button wire:click="delete({{ $member->id }})"
                                        class="px-3 py-1 bg-red-500 text-white text-sm rounded-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                        @can('delete-staff')
                                        >
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 flex items-center justify-between">
        <span class="text-sm text-gray-500">
            Showing {{$staff->firstItem()}} to {{$staff->lastItem()}} of {{$staff->total()}} entries
        </span>
        <div>
            {{ $staff->links() }}
        </div>
    </div>

    <!-- Create Modal -->
    <div wire:ignore.self class="fixed z-50 inset-0 overflow-y-auto hidden @if($showCreateModal) flex @endif" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="relative w-full max-w-md max-h-full">
            <!-- Content -->
            <div class="relative bg-white rounded-lg shadow">
                <!-- Header -->
                <div class="flex items-start justify-between p-4 border-b rounded-t">
                    <h3 class="text-lg font-medium text-gray-900" id="modal-title">
                        Add Staff Member
                    </h3>
                    <button type="button"
                            class="flex h-8 w-8 text-gray-400 bg-transparent rounded-md hover:bg-gray-200 hover:text-gray-900"
                            wire:click="showCreateModal = false"
                            >
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <!-- Body -->
                <div class="p-6 space-y-6">
                    <form wire:submit.prevent="create">
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="company_id" class="block text-sm font-medium text-gray-700 mb-1">Company</label>
                                <select id="company_id"
                                        wire:model="company_id"
                                        class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        required>
                                    <option value="">Select a company</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                                @error('company_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                                    <input type="text"
                                           id="first_name"
                                           wire:model="first_name"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                           required>
                                    @error('first_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                                    <input type="text"
                                           id="last_name"
                                           wire:model="last_name"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                           required>
                                    @error('last_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email"
                                       id="email"
                                       wire:model="email"
                                       class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                       required>
                                @error('email') <span class="text-red-500 text-sm>{{ $message }}</span> @enderror
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1>Phone</label>
                                    <input type="tel"
                                           id="phone"
                                           wire:model="phone"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="position" class="block text-sm font-medium text-gray-700 mb-1>Position</label>
                                    <input type="text"
                                           id="position"
                                           wire:model="position"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="hire_date" class="block text-sm font-medium text-gray-700 mb-1>Hire Date</label>
                                    <input type="date"
                                           id="hire_date"
                                           wire:model="hire_date"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="salary" class="block text-sm font-medium text-gray-700 mb-1>Salary</label>
                                    <input type="number"
                                           id="salary"
                                           wire:model="salary"
                                           step="0.01"
                                           min="0"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="employment_type" class="block text-sm font-medium text-gray-700 mb-1>Employment Type</label>
                                    <select id="employment_type"
                                            wire:model="employment_type"
                                            class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                            required>
                                        <option value="full_time">Full Time</option>
                                        <option value="part_time">Part Time</option>
                                        <option value="contract">Contract</option>
                                        <option value="intern">Intern</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1>Status</label>
                                    <select id="status"
                                            wire:model="status"
                                            class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                            required>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="on_leave">On Leave</option>
                                        <option value="terminated">Terminated</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1>Notes</label>
                                <textarea id="notes"
                                          wire:model="notes"
                                          rows="3"
                                          class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                            </div>
                        </div>
                        <div class="pt-4">
                            <button type="submit"
                                    class="w-full px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Add Staff Member
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
             wire:click="showCreateModal = false"
        ></div>
    </div>

    <!-- Edit Modal -->
    <div wire:ignore.self class="fixed z-50 inset-0 overflow-y-auto hidden @if($showEditModal) flex @endif" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="relative w-full max-w-md max-h-full">
            <!-- Content -->
            <div class="relative bg-white rounded-lg shadow">
                <!-- Header -->
                <div class="flex items-start justify-between p-4 border-b rounded-t">
                    <h3 class="text-lg font-medium text-gray-900" id="modal-title">
                        Edit Staff Member
                    </h3>
                    <button type="button"
                            class="flex h-8 w-8 text-gray-400 bg-transparent rounded-md hover:bg-gray-200 hover:text-gray-900"
                            wire:click="showEditModal = false"
                            >
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <!-- Body -->
                <div class="p-6 space-y-6">
                    <form wire:submit.prevent="update">
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="company_id" class="block text-sm font-medium text-gray-700 mb-1>Company</label>
                                <select id="company_id"
                                        wire:model="company_id"
                                        class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        required>
                                    <option value="">Select a company</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                                @error('company_id') <span class="text-red-500 text-sm>{{ $message }}</span> @enderror
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1>First Name</label>
                                    <input type="text"
                                           id="first_name"
                                           wire:model="first_name"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                           required>
                                    @error('first_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1>Last Name</label>
                                    <input type="text"
                                           id="last_name"
                                           wire:model="last_name"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                           required>
                                    @error('last_name') <span class="text-red-500 text-sm>{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1>Email</label>
                                <input type="email"
                                       id="email"
                                       wire:model="email"
                                       class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                       required>
                                @error('email') <span class="text-red-500 text-sm>{{ $message }}</span> @enderror
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1>Phone</label>
                                    <input type="tel"
                                           id="phone"
                                           wire:model="phone"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="position" class="block text-sm font-medium text-gray-700 mb-1>Position</label>
                                    <input type="text"
                                           id="position"
                                           wire:model="position"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="hire_date" class="block text-sm font-medium text-gray-700 mb-1>Hire Date</label>
                                    <input type="date"
                                           id="hire_date"
                                           wire:model="hire_date"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="salary" class="block text-sm font-medium text-gray-700 mb-1>Salary</label>
                                    <input type="number"
                                           id="salary"
                                           wire:model="salary"
                                           step="0.01"
                                           min="0"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="employment_type" class="block text-sm font-medium text-gray-700 mb-1>Employment Type</label>
                                    <select id="employment_type"
                                            wire:model="employment_type"
                                            class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                            required>
                                        <option value="full_time">Full Time</option>
                                        <option value="part_time">Part Time</option>
                                        <option value="contract">Contract</option>
                                        <option value="intern">Intern</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1>Status</label>
                                    <select id="status"
                                            wire:model="status"
                                            class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                            required>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="on_leave">On Leave</option>
                                        <option value="terminated">Terminated</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1>Notes</label>
                                <textarea id="notes"
                                          wire:model="notes"
                                          rows="3"
                                          class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                            </div>
                        </div>
                        <div class="pt-4">
                            <button type="submit"
                                    class="w-full px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Update Staff Member
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
             wire:click="showEditModal = false"
        ></div>
    </div>
</div>