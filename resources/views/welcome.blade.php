<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WeManage — MSP IT Asset Management</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-900 text-white antialiased">

    {{-- Nav --}}
    <nav class="flex items-center justify-between px-8 py-5 border-b border-slate-800">
        <img src="{{ asset('wemanage-logo.png') }}" alt="WeManage" class="h-8 w-auto brightness-0 invert">
        <div class="flex items-center gap-3">
            @auth
                <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-rose-500 hover:bg-rose-600 text-white text-sm font-medium rounded-lg transition">
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="text-sm text-slate-300 hover:text-white transition">Log in</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="px-4 py-2 bg-rose-500 hover:bg-rose-600 text-white text-sm font-medium rounded-lg transition">
                        Get Started
                    </a>
                @endif
            @endauth
        </div>
    </nav>

    {{-- Hero --}}
    <section class="flex flex-col items-center justify-center text-center px-6 py-28">
        <span class="text-xs font-semibold tracking-widest text-rose-400 uppercase mb-4">MSP IT Asset Management</span>
        <h1 class="text-5xl md:text-6xl font-bold leading-tight max-w-3xl mb-6">
            Manage Your IT Assets <span class="text-rose-400">Smarter</span>
        </h1>
        <p class="text-slate-400 text-lg max-w-xl mb-10">
            Track devices, staff, companies, and warranties — all in one place. Built for Managed Service Providers.
        </p>
        <div class="flex gap-4">
            @auth
                <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-rose-500 hover:bg-rose-600 text-white font-semibold rounded-xl transition">
                    Go to Dashboard →
                </a>
            @else
                <a href="{{ route('login') }}" class="px-6 py-3 bg-rose-500 hover:bg-rose-600 text-white font-semibold rounded-xl transition">
                    Log In →
                </a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="px-6 py-3 border border-slate-600 hover:border-slate-400 text-slate-300 hover:text-white font-semibold rounded-xl transition">
                        Create Account
                    </a>
                @endif
            @endauth
        </div>
    </section>

    {{-- Features --}}
    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 px-8 pb-24 max-w-6xl mx-auto">
        @foreach ([
            ['🏢', 'Companies',   'Manage multiple client companies with full contact and status tracking.'],
            ['👥', 'Staff',       'Track employees per company with position, hire date, and employment details.'],
            ['💻', 'Devices',     'Full hardware inventory with warranty tracking, IP, MAC, and assignment history.'],
            ['🔔', 'Alerts',      'Automatic warranty expiry notifications so nothing slips through the cracks.'],
        ] as [$icon, $title, $desc])
        <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6">
            <div class="text-3xl mb-3">{{ $icon }}</div>
            <h3 class="text-white font-semibold text-lg mb-2">{{ $title }}</h3>
            <p class="text-slate-400 text-sm leading-relaxed">{{ $desc }}</p>
        </div>
        @endforeach
    </section>

    {{-- Footer --}}
    <footer class="border-t border-slate-800 py-6 text-center text-slate-500 text-sm">
        &copy; {{ date('Y') }} WeManage Networks. All rights reserved.
    </footer>

</body>
</html>
