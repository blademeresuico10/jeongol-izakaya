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

<body class="flex flex-col min-h-screen bg-gray-50 text-center font-sans">

  <!-- Header -->
  <header class="p-4 font-bold flex flex-col items-center text-center">
    <p class="text-2xl sm:text-3xl md:text-4xl font-extrabold mb-7">
      Welcome to Jeongol Izakaya
    </p>

    <button id="openLocation" class="mt-3 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
      Location
    </button>
  </header>


  <!-- Reserve Now -->
  <main class="flex-1">
    <a href="{{ route('customer.place_reservation') }}"
      class="inline-block my-4 px-6 py-3 bg-orange-600 text-white font-bold rounded hover:bg-orange-600 transition">
      Reserve Now!
    </a>

    <!-- Gallery -->
    <div class="flex flex-wrap justify-center gap-4 px-4">
      <img src="" alt="Samgyupsal" class="w-48 h-auto rounded bg-gray-200">
      <img src="" alt="Hotpot" class="w-48 h-auto rounded bg-gray-200">
      <img src="" alt="Fusion" class="w-48 h-auto rounded bg-gray-200">
    </div>

    <!-- Feedback Button -->
    <button id="openFeedback"
      class="mt-8 mb-4 px-5 py-2 bg-red-600 text-white font-bold rounded hover:bg-red-700 transition">
      Submit Feedback
    </button>
  </main>

  <!-- Location Modal -->
  <div id="locationModal" class="fixed inset-0 hidden bg-black/50 flex items-center justify-center p-4 z-50">
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
  <div id="feedbackModal" class="fixed inset-0 hidden bg-black bg-opacity-50 items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg">
      <div class="flex justify-between items-center p-4 border-b">
        <h2 class="text-lg font-bold">Submit Feedback</h2>
        <button class="text-gray-500 hover:text-gray-800" onclick="closeModal('feedbackModal')">&times;</button>
      </div>
      <div class="p-4">
        <form>
          <input type="text" placeholder="Your feedback..."
            class="w-full p-2 border rounded mb-3 focus:ring focus:ring-red-300">
          <button type="submit"
            class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition">Submit</button>
        </form>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="bg-gray-900 text-white py-4">
    <div class="max-w-7xl mx-auto px-4 text-center">
      <p class="mb-2 text-lg font-semibold">Contact us</p>

      <div class="flex justify-center gap-4 mb-3">
        <!-- Facebook -->
        <a href="https://www.facebook.com/jeongol.izakaya" target="_blank" rel="noopener noreferrer"
          aria-label="Facebook"
          class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition transform hover:scale-105">
          <i class="fab fa-facebook-f text-lg"></i>
        </a>

        <!-- Instagram -->
        <a href="#" aria-label="Instagram"
          class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition transform hover:scale-105">
          <i class="fab fa-instagram text-lg"></i>
        </a>

        <!-- Twitter -->
        <a href="#" aria-label="Twitter"
          class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition transform hover:scale-105">
          <i class="fab fa-twitter text-lg"></i>
        </a>

        <!-- Email -->
        <a href="mailto:info@jeongolizakaya.com" aria-label="Email"
          class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition transform hover:scale-105">
          <i class="fas fa-envelope text-lg"></i>
        </a>
      </div>

      <p class="text-sm">&copy; {{ date('Y') }} Jeongol Izakaya. All rights reserved.</p>
    </div>
  </footer>

  <!-- Modal Script -->
  <script>
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
  </script>

</body>

</html>