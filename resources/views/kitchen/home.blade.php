<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Kitchen Dashboard</title>
  <link rel="shortcut icon" type="x-icon" href="{{ asset('logo/jeongol_logo.jpg') }}">
  @vite('resources/css/app.css')

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f8fafc;
    }

    /* Smooth expand animation */
    [x-cloak] {
      display: none;
    }

    .expand {
      transition: all 0.25s ease-in-out;
    }
  </style>
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

      <div x-show="open" x-transition x-cloak
        class="absolute right-0 mt-2 w-56 bg-white border rounded-lg shadow-lg z-50">
        <div class="px-4 py-3 border-b">
          <p class="text-sm font-medium">{{ Auth::user()->firstname }} {{ Auth::user()->lastname }}</p>
          <p class="text-xs text-gray-500">{{ Auth::user()->role }}</p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100">Logout</button>
        </form>
      </div>
    </div>
  </div>

  <div class="border-b border-gray-300 mb-6"></div>

  <div class="flex gap-6 max-w-9xl mx-auto items-start">

    <div class="bg-white border rounded-xl shadow-lg w-2/3 flex flex-col">
      <div class="border-b bg-gray-100 rounded-t-xl">
        <p class="p-4 text-lg font-semibold text-gray-700">Current Active Orders</p>
      </div>

      <div class="overflow-y-auto max-h-[75vh] p-4 space-y-4">

        <div x-data="{ open: false }" class="border rounded-lg p-4 shadow-sm hover:shadow-md transition cursor-pointer"
          @click="open = !open">
          <div class="flex justify-between items-center">
            <h3 class="font-semibold text-gray-800">Table #</h3>
            <span class="text-sm text-gray-500">Time</span>
          </div>

          <div x-show="open" x-transition x-cloak
            class="mt-3 text-sm text-gray-600 border-t border-gray-200 pt-3 expand bg-gray-50 rounded-md p-3">
            <p class="font-medium text-blue-600">In Progress</p>
            <ul class="list-disc list-inside ml-2 mt-1 space-y-1">
              <li>Orders</li>
            </ul>
          </div>
        </div>

        <div x-data="{ open: false }" class="border rounded-lg p-4 shadow-sm hover:shadow-md transition cursor-pointer"
          @click="open = !open">
          <div class="flex justify-between items-center">
            <h3 class="font-semibold text-gray-800">Table #</h3>
            <span class="text-sm text-gray-500">Time</span>
          </div>

          <div x-show="open" x-transition x-cloak
            class="mt-3 text-sm text-gray-600 border-t border-gray-200 pt-3 expand bg-gray-50 rounded-md p-3">
            <p class="font-medium text-blue-600">In Progress</p>
            <ul class="list-disc list-inside ml-2 mt-1 space-y-1">
              <li>Orders</li>
            </ul>
          </div>
        </div>


        <div x-data="{ open: false }" class="border rounded-lg p-4 shadow-sm hover:shadow-md transition cursor-pointer"
          @click="open = !open">
          <div class="flex justify-between items-center">
            <h3 class="font-semibold text-gray-800">Table #</h3>
            <span class="text-sm text-gray-500">Time</span>
          </div>

          <div x-show="open" x-transition x-cloak
            class="mt-3 text-sm text-gray-600 border-t border-gray-200 pt-3 expand bg-gray-50 rounded-md p-3">
            <p class="font-medium text-blue-600">In Progress</p>
            <ul class="list-disc list-inside ml-2 mt-1 space-y-1">
              <li>Orders</li>
            </ul>
          </div>
        </div>

        <div x-data="{ open: false }" class="border rounded-lg p-4 shadow-sm hover:shadow-md transition cursor-pointer"
          @click="open = !open">
          <div class="flex justify-between items-center">
            <h3 class="font-semibold text-gray-800">Table #</h3>
            <span class="text-sm text-gray-500">Time</span>
          </div>

          <div x-show="open" x-transition x-cloak
            class="mt-3 text-sm text-gray-600 border-t border-gray-200 pt-3 expand bg-gray-50 rounded-md p-3">
            <p class="font-medium text-blue-600">In Progress</p>
            <ul class="list-disc list-inside ml-2 mt-1 space-y-1">
              <li>Orders</li>
            </ul>
          </div>
        </div>

        <div x-data="{ open: false }" class="border rounded-lg p-4 shadow-sm hover:shadow-md transition cursor-pointer"
          @click="open = !open">
          <div class="flex justify-between items-center">
            <h3 class="font-semibold text-gray-800">Table #</h3>
            <span class="text-sm text-gray-500">Time</span>
          </div>

          <div x-show="open" x-transition x-cloak
            class="mt-3 text-sm text-gray-600 border-t border-gray-200 pt-3 expand bg-gray-50 rounded-md p-3">
            <p class="font-medium text-blue-600">In Progress</p>
            <ul class="list-disc list-inside ml-2 mt-1 space-y-1">
              <li>Orders</li>
            </ul>
          </div>
        </div>

        <div x-data="{ open: false }" class="border rounded-lg p-4 shadow-sm hover:shadow-md transition cursor-pointer"
          @click="open = !open">
          <div class="flex justify-between items-center">
            <h3 class="font-semibold text-gray-800">Table #</h3>
            <span class="text-sm text-gray-500">Time</span>
          </div>

          <div x-show="open" x-transition x-cloak
            class="mt-3 text-sm text-gray-600 border-t border-gray-200 pt-3 expand bg-gray-50 rounded-md p-3">
            <p class="font-medium text-blue-600">In Progress</p>
            <ul class="list-disc list-inside ml-2 mt-1 space-y-1">
              <li>Orders</li>
            </ul>
          </div>
        </div>

        <div x-data="{ open: false }" class="border rounded-lg p-4 shadow-sm hover:shadow-md transition cursor-pointer"
          @click="open = !open">
          <div class="flex justify-between items-center">
            <h3 class="font-semibold text-gray-800">Table #</h3>
            <span class="text-sm text-gray-500">Time</span>
          </div>

          <div x-show="open" x-transition x-cloak
            class="mt-3 text-sm text-gray-600 border-t border-gray-200 pt-3 expand bg-gray-50 rounded-md p-3">
            <p class="font-medium text-blue-600">In Progress</p>
            <ul class="list-disc list-inside ml-2 mt-1 space-y-1">
              <li>Orders</li>
            </ul>
          </div>
        </div>

        <div x-data="{ open: false }" class="border rounded-lg p-4 shadow-sm hover:shadow-md transition cursor-pointer"
          @click="open = !open">
          <div class="flex justify-between items-center">
            <h3 class="font-semibold text-gray-800">Table #</h3>
            <span class="text-sm text-gray-500">Time</span>
          </div>

          <div x-show="open" x-transition x-cloak
            class="mt-3 text-sm text-gray-600 border-t border-gray-200 pt-3 expand bg-gray-50 rounded-md p-3">
            <p class="font-medium text-blue-600">In Progress</p>
            <ul class="list-disc list-inside ml-2 mt-1 space-y-1">
              <li>Orders</li>
            </ul>
          </div>
        </div>

        <div x-data="{ open: false }" class="border rounded-lg p-4 shadow-sm hover:shadow-md transition cursor-pointer"
          @click="open = !open">
          <div class="flex justify-between items-center">
            <h3 class="font-semibold text-gray-800">Table #</h3>
            <span class="text-sm text-gray-500">Time</span>
          </div>

          <div x-show="open" x-transition x-cloak
            class="mt-3 text-sm text-gray-600 border-t border-gray-200 pt-3 expand bg-gray-50 rounded-md p-3">
            <p class="font-medium text-blue-600">In Progress</p>
            <ul class="list-disc list-inside ml-2 mt-1 space-y-1">
              <li>Orders</li>
            </ul>
          </div>
        </div>

        <div x-data="{ open: false }" class="border rounded-lg p-4 shadow-sm hover:shadow-md transition cursor-pointer"
          @click="open = !open">
          <div class="flex justify-between items-center">
            <h3 class="font-semibold text-gray-800">Table #</h3>
            <span class="text-sm text-gray-500">Time</span>
          </div>

          <div x-show="open" x-transition x-cloak
            class="mt-3 text-sm text-gray-600 border-t border-gray-200 pt-3 expand bg-gray-50 rounded-md p-3">
            <p class="font-medium text-blue-600">In Progress</p>
            <ul class="list-disc list-inside ml-2 mt-1 space-y-1">
              <li>Orders</li>
            </ul>
          </div>
        </div>

        <div x-data="{ open: false }" class="border rounded-lg p-4 shadow-sm hover:shadow-md transition cursor-pointer"
          @click="open = !open">
          <div class="flex justify-between items-center">
            <h3 class="font-semibold text-gray-800">Table #</h3>
            <span class="text-sm text-gray-500">Time</span>
          </div>

          <div x-show="open" x-transition x-cloak
            class="mt-3 text-sm text-gray-600 border-t border-gray-200 pt-3 expand bg-gray-50 rounded-md p-3">
            <p class="font-medium text-blue-600">In Progress</p>
            <ul class="list-disc list-inside ml-2 mt-1 space-y-1">
              <li>Orders</li>
            </ul>
          </div>
        </div>

      </div>
    </div>

    <div class="bg-white border rounded-xl shadow-lg w-1/6 flex flex-col">
      <div class="border-b bg-gray-100 rounded-t-xl">
        <p class="p-4 text-lg font-semibold text-gray-700">Order Queue</p>
      </div>

      <div class="p-4 overflow-y-auto max-h-[75vh] space-y-3">
        <ul>
          <li class="border rounded-lg p-3 shadow-sm hover:shadow-md transition cursor-pointer">
            <p>Order #</p>
            <p>Table #</p>
          </li>
        </ul>

      </div>
    </div>

    <div class="bg-white border rounded-xl shadow-lg w-1/6 flex flex-col">
      <!-- Header -->
      <div class="border-b bg-gray-100 rounded-t-xl">
        <p class="p-4 text-lg font-semibold text-gray-700">Added Plates</p>
      </div>

      <!-- Form -->
      <div class="p-4">
        <form action="" class="space-y-5">

          <div>
            <label for="ingredient_name" class="block text-sm font-medium text-gray-700">Plate Name</label>
            <select name="ingredient_name" id="ingredient_name"
              class="mt-1 w-full px-3 py-2 border rounded-lg text-base focus:ring-2 focus:ring-blue-500 focus:outline-none">
              <option value="" disabled selected>Select an ingredient</option>
              <option value="1">Ingredient 1</option>
              <option value="2">Ingredient 2</option>
              <option value="3">Ingredient 3</option>
            </select>
          </div>

          <!-- Table -->
          <div>
            <label for="table" class="block text-sm font-medium text-gray-700">Table</label>
            <select name="table" id="table"
              class="mt-1 w-full px-3 py-2 border rounded-lg text-base focus:ring-2 focus:ring-blue-500 focus:outline-none">
              <option value="" disabled selected>Select a table</option>
              <option value="1">Table 1</option>
              <option value="2">Table 2</option>
              <option value="3">Table 3</option>
            </select>
          </div>

          <div>
            <label for="quantity" class="block text-sm font-medium text-gray-700">Added Quantity</label>
            <input type="number" name="quantity" id="quantity"
              class="mt-1 w-full px-3 py-2 border rounded-lg  focus:ring-2 focus:ring-blue-500 focus:outline-none"
              placeholder="0">
          </div>

          <div>
            <label for="unit">Select a Unit</label>
            <select name="unit" id="unit"
              class="mt-1 w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
              <option value="" disabled selected>Select a unit</option>
              <option value="1">Plate</option>
              <option value="2">Pieces</option>
            </select>
          </div>

          <div>
            <button type="submit"
              class="w-full bg-blue-600 text-white py-2 rounded-lg font-semibold  ">
              Save
            </button>
          </div>

        </form>
      </div>

    </div>

  </div>

  </div>

</body>

</html>