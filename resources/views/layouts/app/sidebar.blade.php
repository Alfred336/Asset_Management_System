<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-slate-100 dark:bg-slate-950">
        <flux:sidebar sticky collapsible="mobile" class="border-e-0 bg-slate-900 dark:bg-slate-950 [&_[data-flux-sidebar-item]]:text-slate-300 [&_[data-flux-sidebar-item]:hover]:bg-slate-800 [&_[data-flux-sidebar-item]:hover]:text-white [&_[data-flux-sidebar-item][aria-current]]:bg-rose-500/20 [&_[data-flux-sidebar-item][aria-current]]:text-rose-400">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="home" :href="route('companies.index')" :current="request()->routeIs('companies.index')" wire:navigate>
                        {{ __('Companies') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="users" :href="route('staff.index')" :current="request()->routeIs('staff.index')" wire:navigate>
                        {{ __('Staff') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="computer-desktop" :href="route('devices.index')" :current="request()->routeIs('devices.index')" wire:navigate>
                        {{ __('Devices') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="user-group" :href="route('users.index')" :current="request()->routeIs('users.index')" wire:navigate>
                        {{ __('Users') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="shield-check" :href="route('roles.index')" :current="request()->routeIs('roles.index')" wire:navigate>
                        {{ __('Roles') }}
                    </flux:sidebar.item>
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
            <header class="hidden lg:flex items-center justify-between px-6 py-3 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 shrink-0">
                <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                    <flux:icon name="home" class="size-4" />
                    <span>/</span>
                    <span class="text-slate-700 dark:text-slate-200 font-medium capitalize">
                        {{ ucfirst(request()->segment(1) ?: 'dashboard') }}
                    </span>
                </div>
                <div class="flex items-center gap-4">
                    <livewire:notification-bell />
                    <span class="text-xs text-slate-400 dark:text-slate-500">{{ now()->format('D, d M Y') }}</span>
                    <flux:dropdown position="bottom" align="end">
                        <flux:profile
                            :name="auth()->user()->name"
                            :initials="auth()->user()->initials()"
                            icon-trailing="chevron-down"
                        />
                        <flux:menu>
                            <div class="px-3 py-2 text-sm">
                                <p class="font-medium text-slate-800 dark:text-white">{{ auth()->user()->name }}</p>
                                <p class="text-slate-500 text-xs">{{ auth()->user()->email }}</p>
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
