<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Wait Staff Dashboard</title>
    <link rel="shortcut icon" type="x-icon" href="{{ asset('logo/jeongol_logo.jpg') }}">

    {{-- Vite & Livewire Styles --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles


    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col">
    <header class="p-3 border-b border-gray-300">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">

            {{-- Logo + Title --}}
            <div class="flex items-center gap-3 ml-5 justify-center sm:justify-start">
                <img src="{{ asset('logo/jeongol_logo.jpg') }}" alt="Jeongol Logo"
                    class="h-12 w-16 sm:h-14 sm:w-20 object-contain">
                <h1 class="text-2xl sm:text-3xl font-semibold text-gray-800">Wait Staff</h1>
            </div>

            {{-- Profile Dropdown --}}
            <div class="relative flex justify-center mr-5" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-2 focus:outline-none">
                    <div
                        class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-gray-300 flex items-center justify-center font-bold text-black text-lg">
                        {{ strtoupper(substr(Auth::user()->firstname, 0, 1)) }}
                    </div>
                </button>

                <div x-show="open" x-transition x-cloak @click.away="open = false"
                    class="absolute right-0 mt-2 w-48 sm:w-56 bg-white border rounded-lg shadow-lg z-50">
                    <div class="px-4 py-3 border-b text-center sm:text-left">
                        <p class="text-sm font-medium">{{ Auth::user()->firstname }} {{ Auth::user()->lastname }}</p>
                        <p class="text-xs text-gray-500">{{ Auth::user()->role }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-center sm:text-left px-4 py-2 hover:bg-gray-100 transition">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-1 w-full px-2 sm:px-4 py-2">
        @livewire('wait-staff-dashboard')
    </main>

    @livewireScripts
</body>

</html>
