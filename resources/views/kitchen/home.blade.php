<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Kitchen</title>
  <link rel="shortcut icon" type="x-icon" href="{{ asset('logo/jeongol_logo.jpg') }}">
  @vite('resources/css/app.css')
  <style>
   
  </style>
</head>

<body class="font-sans p-5 bg-gray-50">
  

  <a class="fixed bottom-5 right-5 bg-red-600 text-white py-2.5 px-4 rounded no-underline font-bold z-50 hover:bg-red-700"
    href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
    Logout
  </a>
  <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>

  <script>
   
  </script>

</body>

</html>