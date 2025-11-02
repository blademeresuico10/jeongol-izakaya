<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Kitchen Dashboard</title>
  <link rel="shortcut icon" type="x-icon" href="{{ asset('logo/jeongol_logo.jpg') }}">

  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f8fafc;
    }

    [x-cloak] {
      display: none;
    }

    .expand {
      transition: all 0.25s ease-in-out;
    }
  </style>

  @livewireStyles

</head>

<body class="p-5">

  <div class="flex justify-between items-center mb-3 px-4">
    <div class="flex items-center gap-2">
      <img src="{{ asset('logo/jeongol_logo.jpg') }}" alt="Jeongol Logo" class="h-14 w-20 object-contain" />
      <h1 class="text-3xl font-semibold text-gray-800">Kitchen Dashboard</h1>
    </div>

    <div class="relative" x-data="{ open: false }">
      <button @click="open = !open" class="flex items-center gap-2 focus:outline-none">
        <div class="w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center font-bold text-black">
          {{ strtoupper(substr(Auth::user()->firstname, 0, 1)) }}
        </div>
      </button>

      <div x-show="open" x-transition x-cloak @click.away="open = false"
        class="absolute right-0 mt-2 w-56 bg-white border rounded-lg shadow-lg z-50">
        <div class="px-4 py-3 border-b">
          <p class="text-sm font-medium">{{ Auth::user()->firstname }} {{ Auth::user()->lastname }}</p>
          <p class="text-xs text-gray-500">{{ Auth::user()->role }}</p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="w-full text-left px-4 py-2">Logout</button>
        </form>
      </div>
    </div>
  </div>

  @livewire('kitchen-dashboard')

  @livewireScripts


</body>

</html>