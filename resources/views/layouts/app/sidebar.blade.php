<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-50 dark:bg-zinc-950">
        <flux:sidebar sticky collapsible="mobile" class="border-e-0 bg-zinc-900 dark:bg-zinc-950 [&_[data-flux-sidebar-item]]:text-zinc-400 [&_[data-flux-sidebar-item]:hover]:bg-white/5 [&_[data-flux-sidebar-item]:hover]:text-white [&_[data-flux-sidebar-item][aria-current]]:bg-brand-500/10 [&_[data-flux-sidebar-item][aria-current]]:text-brand-400">
            <flux:sidebar.header class="flex items-center gap-2">
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')" class="grid gap-0.5">
                    @can('view-dashboard')
                     <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                         {{ __('Dashboard') }}
                     </flux:sidebar.item>
                     @endcan
                     @can('edit-devices')
                     <flux:sidebar.item icon="bolt" :href="route('technician.dashboard')" :current="request()->routeIs('technician.dashboard')" wire:navigate>
                         {{ __('Quick Status') }}
                     </flux:sidebar.item>
                     @endcan
                     @can('view-companies')
                     <flux:sidebar.item icon="building-office" :href="route('companies.index')" :current="request()->routeIs('companies.index')" wire:navigate>
                         {{ __('Companies') }}
                     </flux:sidebar.item>
                     @endcan
                     @can('view-staff')
                    <flux:sidebar.item icon="users" :href="route('staff.index')" :current="request()->routeIs('staff.index')" wire:navigate>
                        {{ __('Staff') }}
                    </flux:sidebar.item>
                     @endcan
                     @can('view-devices')
                    <flux:sidebar.item icon="computer-desktop" :href="route('devices.index')" :current="request()->routeIs('devices.index')" wire:navigate>
                        {{ __('Devices') }}
                    </flux:sidebar.item>
                    @endcan
                    @can('view-users')
                    <flux:sidebar.item icon="user-group" :href="route('users.index')" :current="request()->routeIs('users.index')" wire:navigate>
                        {{ __('Users') }}
                    </flux:sidebar.item>
                    @endcan
                    @can('view-roles')
                    <flux:sidebar.item icon="shield-check" :href="route('roles.index')" :current="request()->routeIs('roles.index')" wire:navigate>
                        {{ __('Roles') }}
                    </flux:sidebar.item>
                    @endcan
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <livewire:notification-bell />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        <!-- Top Header Bar -->
        <flux:main class="flex flex-col">
            <header class="hidden lg:flex items-center justify-between px-8 py-4 bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 shrink-0">
                <div class="flex items-center gap-3 text-sm text-zinc-500 dark:text-zinc-400">
                    <flux:icon name="command-line" class="size-4" />
                    <span class="text-zinc-300 dark:text-zinc-700">/</span>
                    <span class="text-zinc-800 dark:text-zinc-200 font-bold tracking-tight capitalize">
                        {{ str_replace('-', ' ', request()->segment(1) ?: 'dashboard') }}
                    </span>
                </div>
                <div class="flex items-center gap-6">
                    <livewire:notification-bell />
                    <div class="h-4 w-px bg-zinc-200 dark:bg-zinc-800"></div>
                    <flux:dropdown position="bottom" align="end">
                        <flux:profile
                            :name="auth()->user()->name"
                            :initials="auth()->user()->initials()"
                            icon-trailing="chevron-down"
                            class="cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors"
                        />
                        <flux:menu class="w-48">
                            <div class="px-3 py-2">
                                <p class="text-xs font-bold text-zinc-400 uppercase tracking-widest mb-1">Account</p>
                                <p class="font-bold text-zinc-900 dark:text-white truncate">{{ auth()->user()->name }}</p>
                            </div>
                            <flux:menu.separator />
                            <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>Settings</flux:menu.item>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">Log out</flux:menu.item>
                            </form>
                        </flux:menu>
                    </flux:dropdown>
                </div>
            </header>

            <div class="flex-1 overflow-auto">
                {{ $slot }}
            </div>
        </flux:main>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
