Try AI directly in your favorite apps … Use Gemini to generate drafts and refine content, plus get Gemini Pro with access to Google's next-gen AI for ₱1,100.00 ₱0 for 1 month
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

<body class="flex flex-col min-h-screen text-center font-sans relative">

  <!-- Background Cover -->
  <div class="absolute inset-0">
    <img src="{{ asset('assets/Front.jpg') }}" alt="Front Cover" class="w-full h-full object-cover">
    <!-- Dark overlay -->
    <div class="absolute inset-0 bg-black/60"></div>
  </div>

  <!-- Page Content (on top of background) -->
  <div class="relative z-10 flex flex-col min-h-screen text-white">

    <!-- Header -->
    <header class="p-6 font-bold flex flex-col items-center text-center">
      <p class="text-3xl sm:text-4xl md:text-5xl font-extrabold mb-6 drop-shadow-lg">
        Welcome to Jeongol Izakaya
      </p>
      <button id="openLocation" class="mt-3 px-6 py-3 bg-orange-600 text-white rounded hover:bg-orange-700 transition">
        Location
      </button>
    </header>

    <!-- Main -->
    <main class="flex-1 flex flex-col items-center justify-center">
      <!-- Reserve Button -->
      <a href="{{ route('customer.place_reservation') }}"
        class="inline-block mb-4 px-8 py-4 bg-green-600 text-white font-bold rounded transition hover:bg-green-700">
        Reserve Now!
      </a>

      <!-- Section Title -->
      <h2 class="text-2xl md:text-3xl font-semibold mb-6">OUR BEST SELLERS</h2>

      <!-- Image Buttons -->
      <div class="flex flex-col sm:flex-row gap-6 sm:gap-10 mb-6 w-full max-w-5xl justify-center">

        <!-- Samgyupsal -->
        <div class="flex flex-col items-center w-full sm:w-1/3">
          <button onclick="toggleDetails('samgyup-details')" class="focus:outline-none">
            <img src="{{ asset('assets/samgyup.png') }}" alt="Samgyupsal"
              class="w-36 h-36 md:w-48 md:h-48 lg:w-56 lg:h-56 rounded-xl shadow-md hover:scale-110 transition">
          </button>
          <p class="mt-2 text-lg font-semibold">Samgyupsal</p>
          <div id="samgyup-details"
            class="max-h-0 overflow-hidden transition-all duration-500 ease-in-out mt-2 text-white text-sm">
            Freshly grilled pork belly served with authentic Korean sides and dipping sauces.
          </div>
        </div>

        <!-- Hotpot -->
        <div class="flex flex-col items-center w-full sm:w-1/3">
          <button onclick="toggleDetails('hotpot-details')" class="focus:outline-none">
            <img src="{{ asset('assets/Hotpot.png') }}" alt="Hotpot"
              class="w-36 h-36 md:w-48 md:h-48 lg:w-56 lg:h-56 rounded-xl shadow-md hover:scale-110 transition">
          </button>
          <p class="mt-2 text-lg font-semibold">Hotpot</p>
          <div id="hotpot-details"
            class="max-h-0 overflow-hidden transition-all duration-500 ease-in-out mt-2 text-white text-sm">
            A hearty broth with fresh vegetables, meats, and noodles for the perfect hotpot experience.
          </div>
        </div>

        <!-- Fusion -->
        <div class="flex flex-col items-center w-full sm:w-1/3">
          <button onclick="toggleDetails('fusion-details')" class="focus:outline-none">
            <img src="{{ asset('assets/Fusion.png') }}" alt="Fusion"
              class="w-36 h-36 md:w-48 md:h-48 lg:w-56 lg:h-56 rounded-xl shadow-md hover:scale-110 transition">
          </button>
          <p class="mt-2 text-lg font-semibold">Fusion</p>
          <div id="fusion-details"
            class="max-h-0 overflow-hidden transition-all duration-500 ease-in-out mt-2 text-white text-sm">
            A delightful mix of Japanese and Korean flavors, crafted into unique fusion dishes.
          </div>
        </div>
      </div>

      <!-- Feedback Button -->
      <button id="openFeedback"
        class="mt-6 px-6 py-3 bg-green-600 text-white font-bold rounded transition hover:bg-green-700">
        Submit Feedback
      </button>
    </main>

    <!-- Footer -->
    <footer class="bg-black/70 text-white py-4">
      <div class="max-w-7xl mx-auto px-4 text-center">
        <p class="mb-2 text-lg font-semibold">Contact us</p>
        <div class="flex justify-center gap-4 mb-3">
          <a href="https://www.facebook.com/jeongol.izakaya" target="_blank" rel="noopener noreferrer"
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

  <!-- Location Modal -->
  <div id="locationModal" class="fixed inset-0 hidden bg-black/70 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl overflow-hidden">
      <div class="flex justify-between items-center p-4 border-b">
        <h2 class="text-lg font-bold">Our Location</h2>
        <button class="text-gray-500 hover:text-gray-800" onclick="closeModal('locationModal')">&times;</button>
      </div>
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d350.3902543246729!2d124.8495126753962!3d6.494669118615421!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x32f819f5ff30d38d%3A0xf462070ae3f3c5f2!2sJeongol%20Izakaya!5e0!3m2!1sen!2sph!4v1755001268091!5m2!1sen!2sph"
        class="w-full h-[450px] border-0" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
      </iframe>
    </div>
  </div>

  <!-- Feedback Modal -->
  <div id="feedbackModal" class="fixed inset-0 hidden bg-black/70 items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg">
      <div class="flex justify-between items-center p-4 border-b">
        <h2 class="text-lg font-bold">Submit Feedback</h2>
        <button class="text-gray-500 hover:text-gray-800" onclick="closeModal('feedbackModal')">&times;</button>
      </div>
      <div class="p-4">
        <form action="{{ route('customer.feedback') }}" method="POST">
          @csrf
          <label for="email" class="block mb-1 font-medium text-left">Email</label>
          <input id="email" type="email" name="email" placeholder="example@example.com"
            class="w-full p-2 border rounded mb-3 " required>

          <label for="message" class="block mb-1 font-medium text-left">Message</label>
          <textarea id="message" name="message" placeholder="Your feedback..." class="w-full p-2 border rounded mb-3 "
            rows="4" required></textarea>

          <button type="submit" class="w-full px-4 py-2 bg-green-500 text-white font-bold rounded transition">
            Submit
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- Alerts -->
  @if(session('success'))
    <div id="successAlert"
    class="fixed top-4 left-1/2 transform -translate-x-1/2 bg-green-600 text-white px-4 py-2 rounded shadow-lg z-50 opacity-100 transition-opacity duration-500">
    {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div id="errorAlert"
    class="fixed top-4 left-1/2 transform -translate-x-1/2 bg-red-900 text-white px-4 py-2 rounded shadow-lg z-50 opacity-100 transition-opacity duration-500">
    {{ session('error') }}
    </div>
  @endif

  <!-- Scripts -->
  <script>
    function toggleDetails(id) {
      const allDetails = document.querySelectorAll('[id$="-details"]');
      allDetails.forEach(div => {
        if (div.id !== id) {
          div.style.maxHeight = '0px';
        }
      });
      const target = document.getElementById(id);
      if (target.style.maxHeight && target.style.maxHeight !== '0px') {
        target.style.maxHeight = '0px';
      } else {
        target.style.maxHeight = target.scrollHeight + 'px';
      }
    }

    function openModal(id) {
      document.getElementById(id).classList.remove('hidden');
      document.getElementById(id).classList.add('flex');
    }
    function closeModal(id) {
      document.getElementById(id).classList.add('hidden');
      document.getElementById(id).classList.remove('flex');
    }
    document.getElementById('openLocation').addEventListener('click', () => openModal('locationModal'));
    document.getElementById('openFeedback').addEventListener('click', () => openModal('feedbackModal'));

    window.addEventListener('DOMContentLoaded', () => {
      const successAlert = document.getElementById('successAlert');
      const errorAlert = document.getElementById('errorAlert');

      [successAlert, errorAlert].forEach(alert => {
        if (alert) {
          setTimeout(() => {
            alert.classList.add('opacity-0');
            setTimeout(() => alert.remove(), 500);
          }, 2000);
        }
      });
    });
  </script>

</body>
</html>