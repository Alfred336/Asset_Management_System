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

    <!-- Search and Create Button -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div class="mb-4 md:mb-0">
            <input type="text"
                   wire:model.debounce.500ms="search"
                   placeholder="Search companies..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <button wire:click="creating"
                    class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    @can('create-companies')
                    >
                Create Company
            </button>
        </div>
    </div>

    <!-- Sortable Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 text-left text-sm font-medium">
                        <button wire:click="sortBy('name')"
                                class="flex items-center">
                            Name
                            <svg class="ml-1 h-4 w-4" @if($sortField === 'name') @if($sortAsc) fill="currentColor" @endif @endif xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 1 0 111.414 0L10 10.586l3.293-3.293a1 1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                        </button>
                    </th>
                    <th class="px-4 py-2 text-left text-sm font-medium">
                        <button wire:click="sortBy('email')"
                                class="flex items-center">
                            Email
                            <svg class="ml-1 h-4 w-4" @if($sortField === 'email') @if($sortAsc) fill="currentColor" @endif @endif xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 1 0 111.414 0L10 10.586l3.293-3.293a1 1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                        </button>
                    </th>
                    <th class="px-4 py-2 text-left text-sm font-medium">Phone</th>
                    <th class="px-4 py-2 text-left text-sm font-medium">Website</th>
                    <th class="px-4 py-2 text-left text-sm font-medium">Status</th>
                    <th class="px-4 py-2 text-left text-sm font-medium">Actions</th>
                </tr>
            </thead>
            <tbody>
                @if($companies->isEmpty())
                    <tr>
                        <td colspan="6" class="px-4 py-2 text-center text-gray-500">
                            No companies found.
                        </td>
                    </tr>
                @else
                    @foreach($companies as $company)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $company->name }}</td>
                            <td class="px-4 py-2">{{ $company->email }}</td>
                            <td class="px-4 py-2">{{ $company->phone ?? 'N/A' }}</td>
                            <td class="px-4 py-2">{{ $company->website ?? 'N/A' }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($company->status === 'active') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($company->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 space-x-2">
                                <!-- Edit Button -->
                                <button wire:click="edit({{ $company->id }})"
                                        class="px-3 py-1 bg-yellow-500 text-white text-sm rounded-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2"
                                        @can('edit-companies')
                                        >
                                    Edit
                                </button>
                                <!-- Delete Button -->
                                <button wire:click="delete({{ $company->id }})"
                                        class="px-3 py-1 bg-red-500 text-white text-sm rounded-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                        @can('delete-companies')
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
            Showing {{$companies->firstItem()}} to {{$companies->lastItem()}} of {{$companies->total()}} entries
        </span>
        <div>
            {{ $companies->links() }}
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
                        Create Company
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
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                <input type="text"
                                       id="name"
                                       wire:model="name"
                                       class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                       required>
                                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email"
                                       id="email"
                                       wire:model="email"
                                       class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                       required>
                                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                    <input type="tel"
                                           id="phone"
                                           wire:model="phone"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="website" class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                                    <input type="url"
                                           id="website"
                                           wire:model="website"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>
                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                <textarea id="address"
                                          wire:model="address"
                                          rows="3"
                                          class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="tax_id" class="block text-sm font-medium text-gray-700 mb-1">Tax ID</label>
                                    <input type="text"
                                           id="tax_id"
                                           wire:model="tax_id"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select id="status"
                                            wire:model="status"
                                            class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="pt-4">
                            <button type="submit"
                                    class="w-full px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Create Company
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
                        Edit Company
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
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                <input type="text"
                                       id="name"
                                       wire:model="name"
                                       class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                       required>
                                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email"
                                       id="email"
                                       wire:model="email"
                                       class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                       required>
                                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
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
                                    <label for="website" class="block text-sm font-medium text-gray-700 mb-1>Website</label>
                                    <input type="url"
                                           id="website"
                                           wire:model="website"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                            </div>
                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-1>Address</label>
                                <textarea id="address"
                                          wire:model="address"
                                          rows="3"
                                          class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="tax_id" class="block text-sm font-medium text-gray-700 mb-1>Tax ID</label>
                                    <input type="text"
                                           id="tax_id"
                                           wire:model="tax_id"
                                           class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1>Status</label>
                                    <select id="status"
                                            wire:model="status"
                                            class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="pt-4">
                            <button type="submit"
                                    class="w-full px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Update Company
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