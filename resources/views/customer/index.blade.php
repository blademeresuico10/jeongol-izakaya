<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Jeongol Izakaya</title>
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('logo/jeongol_logo.jpg') }}">
  @vite('resources/css/app.css')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="flex flex-col min-h-screen font-sans relative text-white">

  <!-- Background -->
  <div class="absolute inset-0">
    <img src="{{ asset('assets/Front.jpg') }}" alt="Front Cover" class="w-full h-full object-cover">
    <div class="absolute inset-0 bg-black/60"></div>
  </div>

  <!-- Page Content -->
  <div class="relative z-10 flex flex-col min-h-screen">

    <!-- Header -->
    <header class="p-6 flex flex-col items-center text-center">
      <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold mb-6 drop-shadow-lg">
        Welcome to Jeongol Izakaya
      </h1>
      <button id="openLocation"
        class="mt-3 px-6 py-3 bg-orange-600 rounded hover:bg-orange-700 transition font-semibold">
        Location
      </button>
    </header>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col items-center justify-center">

      <!-- Reserve Button -->
      <a href="{{ route('customer.place_reservation') }}"
        class="mb-4 px-8 py-4 bg-green-600 rounded font-bold hover:bg-green-700 transition">
        Reserve Now!
      </a>

      <!-- Section Title -->
      <h2 class="text-2xl md:text-3xl font-semibold mb-6">OUR BEST SELLERS</h2>

      <!-- Best Seller Items -->
      <div class="flex flex-col sm:flex-row gap-6 sm:gap-10 mb-6 w-full max-w-5xl justify-center">

        <!-- Samgyupsal -->
        <div class="flex flex-col items-center w-full sm:w-1/3">
          <button onclick="toggleDetails('samgyup-details')" >
            <img src="{{ asset('assets/samgyup.png') }}" alt="Samgyupsal"
              class="w-36 h-36 md:w-48 md:h-48 lg:w-56 lg:h-56 rounded-xl hover:scale-110 transition">
          </button>
          <p class="mt-2 text-lg font-semibold">Samgyupsal</p>
          <div id="samgyup-details"
            class="max-h-0 overflow-hidden transition-all duration-500 ease-in-out mt-2 text-sm text-white">
            Freshly grilled pork belly served with authentic Korean sides and dipping sauces.
          </div>
        </div>

        <!-- Hotpot -->
        <div class="flex flex-col items-center w-full sm:w-1/3">
          <button onclick="toggleDetails('hotpot-details')" >
            <img src="{{ asset('assets/Hotpot.png') }}" alt="Hotpot"
              class="w-36 h-36 md:w-48 md:h-48 lg:w-56 lg:h-56 rounded-xl  hover:scale-110 transition">
          </button>
          <p class="mt-2 text-lg font-semibold">Hotpot</p>
          <div id="hotpot-details"
            class="max-h-0 overflow-hidden transition-all duration-500 ease-in-out mt-2 text-sm text-white">
            A hearty broth with fresh vegetables, meats, and noodles for the perfect hotpot experience.
          </div>
        </div>

        <!-- Fusion -->
        <div class="flex flex-col items-center w-full sm:w-1/3">
          <button onclick="toggleDetails('fusion-details')" >
            <img src="{{ asset('assets/Fusion.png') }}" alt="Fusion"
              class="w-36 h-36 md:w-48 md:h-48 lg:w-56 lg:h-56 rounded-xl  hover:scale-110 transition">
          </button>
          <p class="mt-2 text-lg font-semibold">Fusion</p>
          <div id="fusion-details"
            class="max-h-0 overflow-hidden transition-all duration-500 ease-in-out mt-2 text-sm text-white">
            A delightful mix of Japanese and Korean flavors, crafted into unique fusion dishes.
          </div>
        </div>

      </div>

      <!-- Feedback Button -->
      <button id="openFeedback"
        class="mt-6 px-6 py-3 bg-green-600 rounded font-bold hover:bg-green-700 transition">
        Submit Feedback
      </button>

    </main>

    <footer class="bg-black/70 py-4 text-white">
      <div class="max-w-7xl mx-auto px-4 text-center">
        <p class="mb-2 text-lg font-semibold">Contact us</p>
        <div class="flex justify-center gap-4 mb-3">
          <a href="https://www.facebook.com/jeongol.izakaya" target="_blank"
            class="w-10 h-10 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center transition transform hover:scale-105">
            <i class="fab fa-facebook-f text-lg"></i>
          </a>
          <a href="#" class="w-10 h-10 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center transition transform hover:scale-105">
            <i class="fab fa-instagram text-lg"></i>
          </a>
          <a href="#" class="w-10 h-10 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center transition transform hover:scale-105">
            <i class="fab fa-twitter text-lg"></i>
          </a>
          <a href="mailto:info@jeongolizakaya.com"
            class="w-10 h-10 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center transition transform hover:scale-105">
            <i class="fas fa-envelope text-lg"></i>
          </a>
        </div>
        <p class="text-sm">&copy; {{ date('Y') }} Jeongol Izakaya. All rights reserved.</p>
      </div>
    </footer>

  </div>

  <div id="locationModal"
    class="fixed inset-0 hidden bg-black/70 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl overflow-hidden">
      <div class="flex justify-between items-center p-4 border-b">
        <h2 class="text-lg font-bold">Our Location</h2>
        <button class="text-gray-500 hover:text-gray-800" onclick="closeModal('locationModal')">&times;</button>
      </div>
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d350.3902543246729!2d124.8495126753962!3d6.494669118615421!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x32f819f5ff30d38d%3A0xf462070ae3f3c5f2!2sJeongol%20Izakaya!5e0!3m2!1sen!2sph!4v1755001268091!5m2!1sen!2sph"
        class="w-full h-96 border-0" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
      </iframe>
    </div>
  </div>

  <div id="feedbackModal"
    class="fixed inset-0 hidden bg-black/70 items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg">
      <div class="flex justify-between items-center p-4 border-b">
        <h2 class="text-lg font-bold">Submit Feedback</h2>
        <button class="text-gray-500 hover:text-gray-800" onclick="closeModal('feedbackModal')">&times;</button>
      </div>
      <div class="p-4">
        <form action="{{ route('customer.feedback') }}" method="POST">
          @csrf
          <label for="email" class="block mb-1 font-medium text-left text-black">Email</label>
          <input id="email" type="email" name="email" placeholder="example@example.com"
            class="w-full p-2 border rounded mb-3 text-black" required>

          <label for="message" class="block mb-1 font-medium text-left text-black ">Message</label>
          <textarea id="message" name="message" placeholder="Your feedback..."
            class="w-full p-2 border rounded mb-3 text-black" rows="4" required></textarea>

          <button type="submit"
            class="w-full px-4 py-2 bg-green-500 rounded font-bold text-white hover:bg-green-600 transition">
            Submit
          </button>
        </form>
      </div>
    </div>
  </div>

  @if(session('success'))
    <div id="successAlert"
      class="fixed top-4 left-1/2 -translate-x-1/2 bg-green-600 text-white px-4 py-2 rounded shadow-lg z-50 transition-opacity duration-500">
      {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div id="errorAlert"
      class="fixed top-4 left-1/2 -translate-x-1/2 bg-red-900 text-white px-4 py-2 rounded shadow-lg z-50 transition-opacity duration-500">
      {{ session('error') }}
    </div>
  @endif

  <script>
    function toggleDetails(id) {
      const allDetails = document.querySelectorAll('[id$="-details"]');
      allDetails.forEach(div => div.id !== id ? div.style.maxHeight = '0px' : null);

      const target = document.getElementById(id);
      target.style.maxHeight = target.style.maxHeight && target.style.maxHeight !== '0px' ? '0px' : target.scrollHeight + 'px';
    }

    function openModal(id) {
      const modal = document.getElementById(id);
      modal.classList.remove('hidden');
      modal.classList.add('flex');
    }

    function closeModal(id) {
      const modal = document.getElementById(id);
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }

    document.getElementById('openLocation').addEventListener('click', () => openModal('locationModal'));
    document.getElementById('openFeedback').addEventListener('click', () => openModal('feedbackModal'));

    window.addEventListener('DOMContentLoaded', () => {
      const successAlert = document.getElementById('successAlert');
      const errorAlert = document.getElementById('errorAlert');

      [successAlert, errorAlert].forEach(alert => {
        if (alert) setTimeout(() => {
          alert.classList.add('opacity-0');
          setTimeout(() => alert.remove(), 500);
        }, 2000);
      });
    });
  </script>

</body>
</html>
