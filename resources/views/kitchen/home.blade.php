<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Kitchen Dashboard</title>
  <link rel="shortcut icon" type="x-icon" href="{{ asset('logo/jeongol_logo.jpg') }}">
  
  {{-- Load Vite compiled assets (includes bootstrap.js) --}}
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

  @livewire('kitchen-dashboard')

  @livewireScripts


</body>
</html>
