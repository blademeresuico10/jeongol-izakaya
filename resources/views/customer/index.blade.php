<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Jeongol Izakaya</title>
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('logo/jeongol_logo.jpg') }}">
  @vite('resources/css/app.css')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    html,
    body {
      min-height: 100%;
      display: flex;
      flex-direction: column;
    }

    body>*:not(footer) {
      flex: 1 0 auto;
    }

    footer {
      flex-shrink: 0;
    }

    .navbar-hidden {
      transform: translateY(-100%);
    }

    .navbar-visible {
      transform: translateY(0);
    }

    .menu-card-wrapper {
      transition: transform 0.3s ease;
    }


    .menu-image {
      transition: transform 0.3s ease, filter 0.3s ease;
    }


    @media only screen and (min-width: 320px) and (max-width: 374px) {}

    @media only screen and (min-width: 375px) and (max-width: 389px) {
      #menuContainer {
        gap: 0.625rem;
      }
    }

    @media only screen and (min-width: 390px) and (max-width: 393px) {
      #menuContainer {
        gap: 0.75rem;
      }
    }

    @media only screen and (min-width: 414px) and (max-width: 427px) {
      #menuContainer {
        gap: 0.75rem;
      }
    }

    @media only screen and (min-width: 428px) and (max-width: 430px) {
      #menuContainer {
        gap: 0.875rem;
      }
    }

    @media only screen and (max-height: 430px) and (orientation: portrait) {


      #menu {
        padding-top: 1rem;
        padding-bottom: 1rem;
      }
    }

    @media (min-width: 768px) {}

    .menu-detail-card {
      transition: all 0.3s ease;
    }


    @media (max-width: 360px) {
      #menuContainer {
        gap: 0.5rem;
      }
    }
  </style>
</head>

<body class="flex flex-col min-h-screen bg-black text-white">

  <!-- Background -->
  <div class="absolute inset-0">
    <div class="absolute inset-0 bg-black/80"></div>
  </div>

  <div class="relative z-10 flex flex-col min-h-screen">

    <!-- Navbar -->
    <nav id="navbar"
      class="fixed top-0 left-0 right-0 z-50 bg-black/80 backdrop-blur-md transition-transform duration-300 navbar-visible">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

          <!-- Logo -->
          <div class="flex items-center">
            <img src="{{ asset('logo/jeongol_logo.jpg') }}" alt="Jeongol Logo" class="h-10 w-10 rounded-full mr-3">
            <span class="text-xl font-bold text-white">Jeongol Izakaya</span>
          </div>

          <!-- Desktop Menu -->
          <div class="hidden md:flex items-center space-x-8">
            <a href="#home" class="text-white transition font-medium">Home</a>
            <a href="#menu" class="text-white  transition font-medium">Menu</a>
            <a href="#about" class="text-white transition font-medium">About</a>
            <button id="navLocation" class="text-white transition font-medium">Location</button>
            <button id="navFeedback" class="text-white transition font-medium">Feedback</button>
          </div>

          <!-- Mobile Menu Button -->
          <button id="mobileMenuBtn" class="md:hidden text-white focus:outline-none">
            <i class="fas fa-bars text-2xl"></i>
          </button>
        </div>
      </div>

      <!-- Mobile Menu -->
      <div id="mobileMenu" class="hidden md:hidden bg-black/95 backdrop-blur-md">
        <div class="px-4 pt-2 pb-4 space-y-3">
          <a href="#home" class="block text-white  transition py-2 font-medium">Home</a>
          <a href="#menu" class="block text-white transition py-2 font-medium">Menu</a>
          <a href="#about" class="block text-white transition py-2 font-medium">About</a>
          <button id="mobileLocation"
            class="block w-full text-left text-white transition py-2 font-medium">Location</button>
          <button id="mobileFeedback"
            class="block w-full text-left text-white transition py-2 font-medium">Feedback</button>
        </div>
      </div>
    </nav>

    <!-- Header -->
    <header id="home" class="mt-16">
      <div class="w-full relative bg-cover bg-center text-white"
        style="background-image: url('{{ asset('assets/sg_bg.png') }}'); height: 250px;">

        <div class="absolute inset-0 bg-black/50"></div>

        <div class="relative z-10 flex flex-col justify-center h-full px-6 max-w-7xl mx-auto">

          <h1 class="text-3xl md:text-4xl font-extrabold mb-2">
            Welcome to Jeongol Izakaya
          </h1>

          <p class="text-lg md:text-xl text-gray-200 mb-6">
            Authentic Korean BBQ & Japanese Fusion Cuisine
          </p>

          <a href="{{ route('customer.place_reservation') }}"
            class="w-fit px-6 py-3 bg-orange-500 rounded-lg font-bold">
            Reserve now!
          </a>

        </div>
      </div>
    </header>

    <main id="menu" class="flex-1 py-6 sm:py-8 px-3 sm:px-4 ">
      <div class="max-w-7xl mx-auto">

        <h2 class="text-lg sm:text-xl md:text-2xl font-bold mb-4 sm:mb-6 text-center">Our Main Course</h2>

        <div class="grid grid-cols-3 md:grid-cols-3 gap-2 sm:gap-3 md:gap-6 max-w-4xl mx-auto" id="menuContainer">

          @forelse($mainMenuItems as $item)
        <div class="menu-card-wrapper">
        <div class="bg-white/5 backdrop-blur-sm border border-white/20 overflow-hidden rounded-lg">

          <div class="relative w-full h-36 xs:h-40 sm:h-48 md:h-56 overflow-hidden">
          <button onclick="toggleDetails('{{ Str::slug($item->menu_item) }}-details')"
            class="w-full h-full focus:outline-none">
            <img src="{{ asset('storage/jeongol_menu/' . $item->image) }}" alt="{{ $item->menu_item }}"
            class="menu-image w-full h-full object-cover">
          </button>
          </div>

          <div class="p-2 sm:p-3 md:p-4">
          <h3 class="text-xs sm:text-sm md:text-base font-bold text-center mb-1 sm:mb-2 line-clamp-2">
            {{ $item->menu_item }}
          </h3>

          <div id="{{ Str::slug($item->menu_item) }}-details"
            class="max-h-0 overflow-hidden transition-all duration-500 ease-in-out text-xs md:text-sm text-gray-300 text-center">
            <p class="pt-2 border-t border-white/10">
            Premium {{ $item->menu_item }} served with authentic Korean sides and dipping sauces.
            </p>
          </div>
          </div>

        </div>
        </div>
      @empty
      @endforelse

        </div>

      </div>
    </main>

    <section class="py-16 px-6 bg-gradient-to-b from-black/40 to-black/60 backdrop-blur-sm">
      <div class="max-w-7xl mx-auto">
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-4 text-orange-400">Menu Inclusions</h2>
        <p class="text-center text-gray-300 mb-12 text-lg">Explore our full range of premium Korean BBQ choices</p>

        <div
          class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-2 sm:gap-3 md:gap-6 max-w-4xl mx-auto px-4">

          <div
            class="menu-detail-card bg-gradient-to-br from-orange-900/30 to-orange-600/20 backdrop-blur-md rounded-2xl p-4 sm:p-6 border border-orange-500/30 hover:border-orange-400/60">
            <div class="flex items-center mb-4">
              <div class="w-10 h-10 sm:w-12 sm:h-12 bg-orange-500 rounded-full flex items-center justify-center mr-3">
                <i class="fas fa-star text-white text-lg sm:text-xl"></i>
              </div>
              <h3 class="text-xl sm:text-2xl font-bold text-orange-300">Special Choices</h3>
            </div>
            <ul class="space-y-1 sm:space-y-2 text-gray-200">
              <li class="flex items-start hover:text-orange-300 transition"><i
                  class="fas fa-chevron-right text-orange-400 mt-1 mr-2 text-xs"></i><span>Cheesy Beef</span></li>
              <li class="flex items-start hover:text-orange-300 transition"><i
                  class="fas fa-chevron-right text-orange-400 mt-1 mr-2 text-xs"></i><span>Plain Moksal</span></li>
              <li class="flex items-start hover:text-orange-300 transition"><i
                  class="fas fa-chevron-right text-orange-400 mt-1 mr-2 text-xs"></i><span>Shrimp Pork Roll</span></li>
              <li class="flex items-start hover:text-orange-300 transition"><i
                  class="fas fa-chevron-right text-orange-400 mt-1 mr-2 text-xs"></i><span>Cheesy Pork</span></li>
              <li class="flex items-start hover:text-orange-300 transition"><i
                  class="fas fa-chevron-right text-orange-400 mt-1 mr-2 text-xs"></i><span>Chicken Kebab</span></li>
              <li class="flex items-start hover:text-orange-300 transition"><i
                  class="fas fa-chevron-right text-orange-400 mt-1 mr-2 text-xs"></i><span>Marinated Moksal</span></li>
              <li class="flex items-start hover:text-orange-300 transition"><i
                  class="fas fa-chevron-right text-orange-400 mt-1 mr-2 text-xs"></i><span>Chicken Yangnyeom</span></li>
              <li class="flex items-start hover:text-orange-300 transition"><i
                  class="fas fa-chevron-right text-orange-400 mt-1 mr-2 text-xs"></i><span>Crab Stick Pork | Beef
                  Roll</span></li>
              <li class="flex items-start hover:text-orange-300 transition"><i
                  class="fas fa-chevron-right text-orange-400 mt-1 mr-2 text-xs"></i><span>Beef Special Cut</span></li>
            </ul>
          </div>

          <div
            class="menu-detail-card bg-gradient-to-br from-pink-900/30 to-pink-600/20 backdrop-blur-md rounded-2xl p-4 sm:p-6 border border-pink-500/30 hover:border-pink-400/60">
            <div class="flex items-center mb-4">
              <div class="w-10 h-10 sm:w-12 sm:h-12 bg-pink-500 rounded-full flex items-center justify-center mr-3">
                <i class="fas fa-bacon text-white text-lg sm:text-xl"></i>
              </div>
              <h3 class="text-xl sm:text-2xl font-bold text-pink-300">Imported Pork</h3>
            </div>
            <ul class="space-y-1 sm:space-y-2 text-gray-200">
              <li class="flex items-start hover:text-pink-300 transition"><i
                  class="fas fa-chevron-right text-pink-400 mt-1 mr-2 text-xs"></i><span>Gochujang</span></li>
              <li class="flex items-start hover:text-pink-300 transition"><i
                  class="fas fa-chevron-right text-pink-400 mt-1 mr-2 text-xs"></i><span>Barbecue</span></li>
              <li class="flex items-start hover:text-pink-300 transition"><i
                  class="fas fa-chevron-right text-pink-400 mt-1 mr-2 text-xs"></i><span>Plain Pork</span></li>
              <li class="flex items-start hover:text-pink-300 transition"><i
                  class="fas fa-chevron-right text-pink-400 mt-1 mr-2 text-xs"></i><span>Special Cut Pork Belly</span>
              </li>
              <li class="flex items-start hover:text-pink-300 transition"><i
                  class="fas fa-chevron-right text-pink-400 mt-1 mr-2 text-xs"></i><span>BBQ Jack Daniels (no
                  alcohol)</span></li>
            </ul>
          </div>

          <div
            class="menu-detail-card bg-gradient-to-br from-red-900/30 to-red-600/20 backdrop-blur-md rounded-2xl p-4 sm:p-6 border border-red-500/30 hover:border-red-400/60">
            <div class="flex items-center mb-4">
              <div class="w-10 h-10 sm:w-12 sm:h-12 bg-red-500 rounded-full flex items-center justify-center mr-3">
                <i class="fas fa-drumstick-bite text-white text-lg sm:text-xl"></i>
              </div>
              <h3 class="text-xl sm:text-2xl font-bold text-red-300">Imported Beef</h3>
            </div>
            <ul class="space-y-1 sm:space-y-2 text-gray-200">
              <li class="flex items-start hover:text-red-300 transition"><i
                  class="fas fa-chevron-right text-red-400 mt-1 mr-2 text-xs"></i><span>Korean Souy Garlic</span></li>
              <li class="flex items-start hover:text-red-300 transition"><i
                  class="fas fa-chevron-right text-red-400 mt-1 mr-2 text-xs"></i><span>Spicy Bulgogi</span></li>
              <li class="flex items-start hover:text-red-300 transition"><i
                  class="fas fa-chevron-right text-red-400 mt-1 mr-2 text-xs"></i><span>Galbi Bulgogi</span></li>
              <li class="flex items-start hover:text-red-300 transition"><i
                  class="fas fa-chevron-right text-red-400 mt-1 mr-2 text-xs"></i><span>Yangnyeom</span></li>
              <li class="flex items-start hover:text-red-300 transition"><i
                  class="fas fa-chevron-right text-red-400 mt-1 mr-2 text-xs"></i><span>Pork Enoki</span></li>
              <li class="flex items-start hover:text-red-300 transition"><i
                  class="fas fa-chevron-right text-red-400 mt-1 mr-2 text-xs"></i><span>Beef Enoki</span></li>
              <li class="flex items-start hover:text-red-300 transition"><i
                  class="fas fa-chevron-right text-red-400 mt-1 mr-2 text-xs"></i><span>Barbecue</span></li>
              <li class="flex items-start hover:text-red-300 transition"><i
                  class="fas fa-chevron-right text-red-400 mt-1 mr-2 text-xs"></i><span>Imported Plain Beef Roll</span>
              </li>
              <li class="flex items-start hover:text-red-300 transition"><i
                  class="fas fa-chevron-right text-red-400 mt-1 mr-2 text-xs"></i><span>BBQ Jack Daniels (no
                  alcohol)</span></li>
              <li class="flex items-start hover:text-red-300 transition"><i
                  class="fas fa-chevron-right text-red-400 mt-1 mr-2 text-xs"></i><span>Gochujang</span></li>
            </ul>
          </div>

          <div
            class="menu-detail-card bg-gradient-to-br from-yellow-900/30 to-yellow-600/20 backdrop-blur-md rounded-2xl p-4 sm:p-6 border border-yellow-500/30 hover:border-yellow-400/60">
            <div class="flex items-center mb-4">
              <div class="w-10 h-10 sm:w-12 sm:h-12 bg-yellow-500 rounded-full flex items-center justify-center mr-3">
                <i class="fas fa-egg text-white text-lg sm:text-xl"></i>
              </div>
              <h3 class="text-xl sm:text-2xl font-bold text-yellow-300">Non-Pork Choices</h3>
            </div>
            <ul class="space-y-1 sm:space-y-2 text-gray-200">
              <li class="flex items-start hover:text-yellow-300 transition"><i
                  class="fas fa-chevron-right text-yellow-400 mt-1 mr-2 text-xs"></i><span>Spicy Chicken</span></li>
              <li class="flex items-start hover:text-yellow-300 transition"><i
                  class="fas fa-chevron-right text-yellow-400 mt-1 mr-2 text-xs"></i><span>Shrimp</span></li>
              <li class="flex items-start hover:text-yellow-300 transition"><i
                  class="fas fa-chevron-right text-yellow-400 mt-1 mr-2 text-xs"></i><span>Plain Chicken</span></li>
              <li class="flex items-start hover:text-yellow-300 transition"><i
                  class="fas fa-chevron-right text-yellow-400 mt-1 mr-2 text-xs"></i><span>Marinated Chicken</span></li>
            </ul>
          </div>

          <div
            class="menu-detail-card bg-gradient-to-br from-green-900/30 to-green-600/20 backdrop-blur-md rounded-2xl p-4 sm:p-6 border border-green-500/30 hover:border-green-400/60">
            <div class="flex items-center mb-4">
              <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-500 rounded-full flex items-center justify-center mr-3">
                <i class="fas fa-bowl-rice text-white text-lg sm:text-xl"></i>
              </div>
              <h3 class="text-xl sm:text-2xl font-bold text-green-300">Rice</h3>
            </div>
            <ul class="space-y-1 sm:space-y-2 text-gray-200">
              <li class="flex items-start hover:text-green-300 transition"><i
                  class="fas fa-chevron-right text-green-400 mt-1 mr-2 text-xs"></i><span>Plain</span></li>
              <li class="flex items-start hover:text-green-300 transition"><i
                  class="fas fa-chevron-right text-green-400 mt-1 mr-2 text-xs"></i><span>Kimchi Rice</span></li>
              <li class="flex items-start hover:text-green-300 transition"><i
                  class="fas fa-chevron-right text-green-400 mt-1 mr-2 text-xs"></i><span>Bibimbap</span></li>
            </ul>
          </div>

          <div
            class="menu-detail-card bg-gradient-to-br from-blue-900/30 to-blue-600/20 backdrop-blur-md rounded-2xl p-4 sm:p-6 border border-blue-500/30 hover:border-blue-400/60">
            <div class="flex items-center mb-4">
              <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                <i class="fas fa-glass-water text-white text-lg sm:text-xl"></i>
              </div>
              <h3 class="text-xl sm:text-2xl font-bold text-blue-300">Drinks & Dessert</h3>
            </div>
            <div class="space-y-4">
              <div>
                <h4 class="text-blue-300 font-semibold mb-2">Drinks</h4>
                <ul class="space-y-1 sm:space-y-2 text-gray-200">
                  <li class="flex items-start hover:text-blue-300 transition"><i
                      class="fas fa-chevron-right text-blue-400 mt-1 mr-2 text-xs"></i><span>Four Seasons Red Iced
                      Tea</span></li>
                </ul>
              </div>
              <div class="pt-3 border-t border-blue-500/30">
                <div class="flex items-center mb-2">
                  <i class="fas fa-ice-cream text-2xl sm:text-3xl text-blue-300 mr-3"></i>
                  <h4 class="text-blue-300 font-bold text-lg sm:text-xl">UNLIMITED ICE CREAM</h4>
                </div>
                <p class="text-gray-300 text-sm sm:text-base italic">Enjoy as much as you want!</p>
              </div>
            </div>
          </div>

        </div>

      </div>
    </section>

    <section id="about" class="py-16 px-4 sm:px-6 bg-black/50 backdrop-blur-sm">
      <div class="max-w-6xl mx-auto">
        <h2 class="text-3xl sm:text-4xl font-bold text-center mb-8">About Jeongol Izakaya</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
          <div class="space-y-4">
            <h3 class="text-2xl sm:text-2xl font-semibold text-orange-400 mb-4">Our Story</h3>
            <p class="text-gray-200 leading-relaxed text-sm sm:text-base">
              Jeongol Izakaya brings together the best of Korean and Japanese culinary traditions in a warm, welcoming
              atmosphere. Founded with a passion for authentic Asian cuisine, we've become a beloved destination for
              food lovers seeking quality, flavor, and an unforgettable dining experience.
            </p>
            <p class="text-gray-200 leading-relaxed text-sm sm:text-base">
              Our name combines "Jeongol" (정골) - a traditional Korean hotpot, with "Izakaya" - a Japanese-style
              gastropub, perfectly representing our fusion approach to dining.
            </p>
          </div>

          <div class="space-y-4">
            <h3 class="text-2xl sm:text-2xl font-semibold text-orange-400 mb-4">What Makes Us Special</h3>
            <ul class="space-y-2 sm:space-y-3 text-gray-200 text-sm sm:text-base">
              <li class="flex items-start">
                <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                <span>Premium quality meats and fresh ingredients sourced daily</span>
              </li>
              <li class="flex items-start">
                <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                <span>Authentic Korean BBQ experience with traditional side dishes (banchan)</span>
              </li>
              <li class="flex items-start">
                <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                <span>Cozy, intimate atmosphere perfect for gatherings and celebrations</span>
              </li>
              <li class="flex items-start">
                <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                <span>Expert chefs with years of experience in Asian cuisine</span>
              </li>
              <li class="flex items-start">
                <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                <span>Unique fusion dishes that blend Korean and Japanese flavors</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <footer class="bg-black/90 text-white w-full fixed bottom-0 left-0">
      <div class="max-w-7xl mx-auto flex flex-row flex-wrap items-center justify-between px-3 py-5 gap-2 text-sm">

        <div class="flex items-center gap-2">
          <a href="https://www.facebook.com/jeongol.izakaya" target="_blank"
            class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center transition transform hover:scale-105">
            <i class="fab fa-facebook-f"></i>
          </a>
          <a href="#"
            class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center transition transform hover:scale-105">
            <i class="fab fa-instagram"></i>
          </a>
          <a href="#"
            class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center transition transform hover:scale-105">
            <i class="fab fa-twitter"></i>
          </a>
          <a href="#"
            class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center transition transform hover:scale-105">
            <i class="fas fa-envelope"></i>
          </a>
        </div>
        <div>
          <p>&copy; {{ date('Y') }} Jeongol Izakaya</p>
        </div>
      </div>
    </footer>



  </div>

  <div id="locationModal" class="fixed inset-0 hidden bg-black/70 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl overflow-hidden">
      <div class="flex justify-between items-center p-4 border-b">
        <h2 class="text-lg font-bold text-black">Our Location</h2>
        <button class="text-gray-500 hover:text-gray-800 text-2xl"
          onclick="closeModal('locationModal')">&times;</button>
      </div>
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d350.3902543246729!2d124.8495126753962!3d6.494669118615421!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x32f819f5ff30d38d%3A0xf462070ae3f3c5f2!2sJeongol%20Izakaya!5e0!3m2!1sen!2sph!4v1755001268091!5m2!1sen!2sph"
        class="w-full h-96 border-0" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
      </iframe>
    </div>
  </div>

  <div id="feedbackModal" class="fixed inset-0 hidden bg-black/70 items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg">
      <div class="flex justify-between items-center p-4 border-b">
        <h2 class="text-lg font-bold text-black">Submit Feedback</h2>
        <button class="text-gray-500 hover:text-gray-800 text-2xl"
          onclick="closeModal('feedbackModal')">&times;</button>
      </div>
      <div class="p-4">
        <form action="{{ route('customer.feedback') }}" method="POST">
          @csrf
          <label for="message" class="block mb-1 font-medium text-left text-black">Message</label>
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
</body>


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
    const modal = document.getElementById(id);
    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }

  function closeModal(id) {
    const modal = document.getElementById(id);
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }

  document.getElementById('mobileMenuBtn').addEventListener('click', () => {
    const menu = document.getElementById('mobileMenu');
    const icon = document.querySelector('#mobileMenuBtn i');
    menu.classList.toggle('hidden');
    
  });

  // Location modal handlers
  document.getElementById('navLocation').addEventListener('click', () => openModal('locationModal'));
  document.getElementById('mobileLocation').addEventListener('click', () => {
    openModal('locationModal');
    document.getElementById('mobileMenu').classList.add('hidden');
  });

  // Feedback modal handlers
  document.getElementById('navFeedback').addEventListener('click', () => openModal('feedbackModal'));
  document.getElementById('mobileFeedback').addEventListener('click', () => {
    openModal('feedbackModal');
    document.getElementById('mobileMenu').classList.add('hidden');
  });

  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        document.getElementById('mobileMenu').classList.add('hidden');
      }
    });
  });

  let lastScroll = 0;
  window.addEventListener('scroll', () => {
    const navbar = document.getElementById('navbar');
    const currentScroll = window.pageYOffset;

    if (currentScroll <= 0) {
      navbar.classList.remove('navbar-hidden');
      navbar.classList.add('navbar-visible');
      return;
    }

    if (currentScroll > lastScroll && currentScroll > 100) {
      navbar.classList.remove('navbar-visible');
      navbar.classList.add('navbar-hidden');
    } else {
      navbar.classList.remove('navbar-hidden');
      navbar.classList.add('navbar-visible');
    }

    lastScroll = currentScroll;
  });

  window.addEventListener('DOMContentLoaded', () => {
    const successAlert = document.getElementById('successAlert');
    const errorAlert = document.getElementById('errorAlert');

    [successAlert, errorAlert].forEach(alert => {
      if (alert) {
        setTimeout(() => {
          alert.classList.add('opacity-0');
          setTimeout(() => alert.remove(), 500);
        }, 3000);
      }
    });
  });
</script>

</html>