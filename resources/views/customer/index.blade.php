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
    .navbar-hidden {
      transform: translateY(-100%);
    }
    
    .navbar-visible {
      transform: translateY(0);
    }

    /* Explosive entrance animation */
    @keyframes explosive-entrance {
      0% {
        opacity: 0;
        transform: scale(0.3) rotate(-15deg) translateY(50px);
        filter: blur(10px);
      }
      60% {
        transform: scale(1.1) rotate(5deg) translateY(-10px);
      }
      100% {
        opacity: 1;
        transform: scale(1) rotate(0deg) translateY(0);
        filter: blur(0);
      }
    }

    /* Floating animation */
    @keyframes float {
      0%, 100% {
        transform: translateY(0px) rotate(0deg);
      }
      33% {
        transform: translateY(-15px) rotate(2deg);
      }
      66% {
        transform: translateY(-8px) rotate(-2deg);
      }
    }

    /* Shimmer effect */
    @keyframes shimmer {
      0% {
        background-position: -200% center;
      }
      100% {
        background-position: 200% center;
      }
    }

    /* Menu card glow pulse */
    @keyframes glow-pulse {
      0%, 100% {
        box-shadow: 0 0 30px rgba(249, 115, 22, 0.6), 0 0 60px rgba(249, 115, 22, 0.3);
        transform: scale(1) rotate(0deg);
      }
      50% {
        box-shadow: 0 0 50px rgba(249, 115, 22, 0.9), 0 0 100px rgba(249, 115, 22, 0.5);
        transform: scale(1.08) rotate(3deg);
      }
    }

    .menu-highlight {
      animation: glow-pulse 1.2s ease-in-out 3;
    }

    /* Fade in animation with explosive entrance */
    .fade-in-up {
      opacity: 1;
      animation: explosive-entrance 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    /* Stagger animation delays */
    .fade-in-up:nth-child(1) { animation-delay: 0.1s; }
    .fade-in-up:nth-child(2) { animation-delay: 0.3s; }
    .fade-in-up:nth-child(3) { animation-delay: 0.5s; }

    /* Continuous floating after entrance */
    .fade-in-up.floating {
      animation: float 3s ease-in-out infinite;
    }

    .fade-in-up:nth-child(1).floating { animation-delay: 0s; }
    .fade-in-up:nth-child(2).floating { animation-delay: 0.5s; }
    .fade-in-up:nth-child(3).floating { animation-delay: 1s; }

    /* Menu card container */
    .menu-card-wrapper {
      position: relative;
      transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .menu-card-wrapper:hover {
      transform: translateY(-20px) scale(1.05);
      z-index: 10;
    }

    /* Shimmer overlay on hover */
    .menu-card-wrapper::before {
      content: '';
      position: absolute;
      top: -2px;
      left: -2px;
      right: -2px;
      bottom: -2px;
      background: linear-gradient(90deg, 
        transparent 0%, 
        rgba(255, 255, 255, 0.3) 50%, 
        transparent 100%);
      background-size: 200% 100%;
      border-radius: 1rem;
      opacity: 0;
      transition: opacity 0.3s;
      pointer-events: none;
      z-index: -1;
      display: none;
    }

    /* Glowing border effect */
    .menu-card-wrapper::after {
      content: '';
      position: absolute;
      top: -3px;
      left: -3px;
      right: -3px;
      bottom: -3px;
      background: linear-gradient(45deg, 
        #ff6b00, #ff8c00, #ffa500, #ff8c00, #ff6b00);
      background-size: 300% 300%;
      border-radius: 1.2rem;
      opacity: 0;
      z-index: -1;
      transition: opacity 0.3s;
      animation: shimmer 3s linear infinite;
      display: none;
    }

    /* Image zoom and glow on hover */
    .menu-image {
      transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
      position: relative;
    }

    .menu-card-wrapper:hover .menu-image {
      transform: scale(1.15) rotate(5deg);
      filter: brightness(1.2) drop-shadow(0 0 30px rgba(249, 115, 22, 0.8));
    }

    /* Title animation */
    .menu-title {
      transition: all 0.3s ease;
      position: relative;
      display: inline-block;
    }

    .menu-card-wrapper:hover .menu-title {
      color: #ff8c00;
      transform: scale(1.1);
      text-shadow: 0 0 20px rgba(255, 140, 0, 0.8);
    }

    /* Parallax effect for background */
    .parallax-bg {
      transition: transform 0.1s ease-out;
    }

    /* Smoke/steam effect */
    @keyframes steam {
      0% {
        transform: translateY(0) scale(1);
        opacity: 0.7;
      }
      100% {
        transform: translateY(-50px) scale(1.5);
        opacity: 0;
      }
    }

    .steam-particle {
      position: absolute;
      width: 30px;
      height: 30px;
      background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, transparent 70%);
      border-radius: 50%;
      pointer-events: none;
      animation: steam 2s ease-out infinite;
    }

    .steam-particle:nth-child(1) { animation-delay: 0s; left: 20%; }
    .steam-particle:nth-child(2) { animation-delay: 0.7s; left: 50%; }
    .steam-particle:nth-child(3) { animation-delay: 1.4s; left: 80%; }
  </style>
</head>

<body class="flex flex-col min-h-screen font-sans relative text-white">

  <div class="absolute inset-0">
    <img src="{{ asset('assets/Front.jpg') }}" alt="Front Cover" class="w-full h-full object-cover">
    <div class="absolute inset-0 bg-black/60"></div>
  </div>

  <div class="relative z-10 flex flex-col min-h-screen">

    <!-- Navbar -->
    <nav id="navbar" class="fixed top-0 left-0 right-0 z-50 bg-black/80 backdrop-blur-md transition-transform duration-300 navbar-visible">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          
          <!-- Logo -->
          <div class="flex items-center">
            <img src="{{ asset('logo/jeongol_logo.jpg') }}" alt="Jeongol Logo" class="h-10 w-10 rounded-full mr-3">
            <span class="text-xl font-bold text-white">Jeongol Izakaya</span>
          </div>

          <!-- Desktop Menu -->
          <div class="hidden md:flex items-center space-x-8">
            <a href="#home" class="text-white hover:text-orange-400 transition font-medium">Home</a>
            <a href="#menu" class="text-white hover:text-orange-400 transition font-medium">Menu</a>
            <a href="#about" class="text-white hover:text-orange-400 transition font-medium">About</a>
            <button id="navLocation" class="text-white hover:text-orange-400 transition font-medium">Location</button>
            <button id="navFeedback" class="text-white hover:text-orange-400 transition font-medium">Feedback</button>
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
          <a href="#home" class="block text-white hover:text-orange-400 transition py-2 font-medium">Home</a>
          <a href="#menu" class="block text-white hover:text-orange-400 transition py-2 font-medium">Menu</a>
          <a href="#about" class="block text-white hover:text-orange-400 transition py-2 font-medium">About</a>
          <button id="mobileLocation" class="block w-full text-left text-white hover:text-orange-400 transition py-2 font-medium">Location</button>
          <button id="mobileFeedback" class="block w-full text-left text-white hover:text-orange-400 transition py-2 font-medium">Feedback</button>
        </div>
      </div>
    </nav>

    <header id="home" class="p-6 flex flex-col items-center text-center mt-16">
      <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold mb-6 drop-shadow-lg">
        Welcome to Jeongol Izakaya
      </h1>
      <button id="openLocation"
        class="mt-3 px-6 py-3 bg-orange-600 rounded hover:bg-orange-700 transition font-semibold">
        Location
      </button>
    </header>

    <main id="menu" class="flex-1 flex flex-col items-center justify-center">

      <a href="{{ route('customer.place_reservation') }}"
        class="mb-4 px-8 py-4 bg-green-600 rounded font-bold hover:bg-green-700 transition">
        Reserve Now!
      </a>

      <h2 class="text-2xl md:text-3xl font-semibold mb-6">OUR MAIN COURSE</h2>

      <div class="flex flex-col sm:flex-row gap-6 sm:gap-10 mb-6 w-full max-w-5xl justify-center" id="menuContainer">

        <div class="flex flex-col items-center w-full sm:w-1/3 fade-in-up menu-card-wrapper">
          <div class="relative">
            <div class="steam-particle"></div>
            <div class="steam-particle"></div>
            <div class="steam-particle"></div>
            <button onclick="toggleDetails('samgyup-details')" class="menu-card relative">
              <img src="{{ asset('assets/samgyup.png') }}" alt="Samgyupsal"
                class="menu-image w-36 h-36 md:w-48 md:h-48 lg:w-56 lg:h-56 rounded-xl">
            </button>
          </div>
          <p class="menu-title mt-2 text-lg font-semibold">Samgyupsal</p>
          <div id="samgyup-details"
            class="max-h-0 overflow-hidden transition-all duration-500 ease-in-out mt-2 text-sm text-white">
            Freshly grilled pork , beef, chicken, and etc., served with authentic Korean sides and dipping sauces.
          </div>
        </div>

        <div class="flex flex-col items-center w-full sm:w-1/3 fade-in-up menu-card-wrapper">
          <div class="relative">
            <div class="steam-particle"></div>
            <div class="steam-particle"></div>
            <div class="steam-particle"></div>
            <button onclick="toggleDetails('hotpot-details')" class="menu-card relative">
              <img src="{{ asset('assets/Hotpot.png') }}" alt="Hotpot"
                class="menu-image w-36 h-36 md:w-48 md:h-48 lg:w-56 lg:h-56 rounded-xl">
            </button>
          </div>
          <p class="menu-title mt-2 text-lg font-semibold">Hotpot</p>
          <div id="hotpot-details"
            class="max-h-0 overflow-hidden transition-all duration-500 ease-in-out mt-2 text-sm text-white">
            A hearty and spicy broth with fresh vegetables, meats, and noodles for the perfect hotpot experience.
          </div>
        </div>

        <div class="flex flex-col items-center w-full sm:w-1/3 fade-in-up menu-card-wrapper">
          <div class="relative">
            <div class="steam-particle"></div>
            <div class="steam-particle"></div>
            <div class="steam-particle"></div>
            <button onclick="toggleDetails('fusion-details')" class="menu-card relative">
              <img src="{{ asset('assets/Fusion.png') }}" alt="Fusion"
                class="menu-image w-36 h-36 md:w-48 md:h-48 lg:w-56 lg:h-56 rounded-xl">
            </button>
          </div>
          <p class="menu-title mt-2 text-lg font-semibold">Fusion</p>
          <div id="fusion-details"
            class="max-h-0 overflow-hidden transition-all duration-500 ease-in-out mt-2 text-sm text-white">
            Combination of Samgyupsal and Hotpot, forming a delightful combo of Japanese and Korean 
          </div>
        </div>

      </div>

      <button id="openFeedback"
        class="mt-4 mb-4 px-6 py-3 bg-green-600 rounded font-bold hover:bg-green-700 transition">
        Submit Feedback
      </button>

    </main>

    <!-- Menu Details Section -->
    <section class="py-16 px-6 bg-gradient-to-b from-black/40 to-black/60 backdrop-blur-sm">
      <div class="max-w-7xl mx-auto">
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-4 text-orange-400">Menu Inclusions</h2>
        <p class="text-center text-gray-300 mb-12 text-lg">Explore our full range of premium Korean BBQ choices</p>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
          
          <!-- Special Choices -->
          <div class="menu-detail-card bg-gradient-to-br from-orange-900/30 to-orange-600/20 backdrop-blur-md rounded-2xl p-6 border border-orange-500/30 hover:border-orange-400/60 transition-all duration-300 transform hover:-translate-y-2 hover:shadow-2xl hover:shadow-orange-500/30">
            <div class="flex items-center mb-4">
              <div class="w-12 h-12 bg-orange-500 rounded-full flex items-center justify-center mr-3 animate-pulse">
                <i class="fas fa-star text-white text-xl"></i>
              </div>
              <h3 class="text-2xl font-bold text-orange-300">Special Choices</h3>
            </div>
            <ul class="space-y-2 text-gray-200">
              <li class="flex items-start hover:text-orange-300 transition">
                <i class="fas fa-chevron-right text-orange-400 mt-1 mr-2 text-xs"></i>
                <span>Cheesy Beef</span>
              </li>
              <li class="flex items-start hover:text-orange-300 transition">
                <i class="fas fa-chevron-right text-orange-400 mt-1 mr-2 text-xs"></i>
                <span>Plain Moksal</span>
              </li>
              <li class="flex items-start hover:text-orange-300 transition">
                <i class="fas fa-chevron-right text-orange-400 mt-1 mr-2 text-xs"></i>
                <span>Shrimp Pork Roll</span>
              </li>
              <li class="flex items-start hover:text-orange-300 transition">
                <i class="fas fa-chevron-right text-orange-400 mt-1 mr-2 text-xs"></i>
                <span>Cheesy Pork</span>
              </li>
              <li class="flex items-start hover:text-orange-300 transition">
                <i class="fas fa-chevron-right text-orange-400 mt-1 mr-2 text-xs"></i>
                <span>Chicken Kebab</span>
              </li>
              <li class="flex items-start hover:text-orange-300 transition">
                <i class="fas fa-chevron-right text-orange-400 mt-1 mr-2 text-xs"></i>
                <span>Marinated Moksal</span>
              </li>
              <li class="flex items-start hover:text-orange-300 transition">
                <i class="fas fa-chevron-right text-orange-400 mt-1 mr-2 text-xs"></i>
                <span>Chicken Yangnyeom</span>
              </li>
              <li class="flex items-start hover:text-orange-300 transition">
                <i class="fas fa-chevron-right text-orange-400 mt-1 mr-2 text-xs"></i>
                <span>Crab Stick Pork | Beef Roll</span>
              </li>
              <li class="flex items-start hover:text-orange-300 transition">
                <i class="fas fa-chevron-right text-orange-400 mt-1 mr-2 text-xs"></i>
                <span>Beef Special Cut</span>
              </li>
            </ul>
          </div>

          <!-- Imported Pork -->
          <div class="menu-detail-card bg-gradient-to-br from-pink-900/30 to-pink-600/20 backdrop-blur-md rounded-2xl p-6 border border-pink-500/30 hover:border-pink-400/60 transition-all duration-300 transform hover:-translate-y-2 hover:shadow-2xl hover:shadow-pink-500/30">
            <div class="flex items-center mb-4">
              <div class="w-12 h-12 bg-pink-500 rounded-full flex items-center justify-center mr-3 animate-pulse">
                <i class="fas fa-bacon text-white text-xl"></i>
              </div>
              <h3 class="text-2xl font-bold text-pink-300">Imported Pork</h3>
            </div>
            <ul class="space-y-2 text-gray-200">
              <li class="flex items-start hover:text-pink-300 transition">
                <i class="fas fa-chevron-right text-pink-400 mt-1 mr-2 text-xs"></i>
                <span>Gochujang</span>
              </li>
              <li class="flex items-start hover:text-pink-300 transition">
                <i class="fas fa-chevron-right text-pink-400 mt-1 mr-2 text-xs"></i>
                <span>Barbecue</span>
              </li>
              <li class="flex items-start hover:text-pink-300 transition">
                <i class="fas fa-chevron-right text-pink-400 mt-1 mr-2 text-xs"></i>
                <span>Plain Pork</span>
              </li>
              <li class="flex items-start hover:text-pink-300 transition">
                <i class="fas fa-chevron-right text-pink-400 mt-1 mr-2 text-xs"></i>
                <span>Special Cut Pork Belly</span>
              </li>
              <li class="flex items-start hover:text-pink-300 transition">
                <i class="fas fa-chevron-right text-pink-400 mt-1 mr-2 text-xs"></i>
                <span>BBQ Jack Daniels (no alcohol)</span>
              </li>
            </ul>
          </div>

          <!-- Imported Beef -->
          <div class="menu-detail-card bg-gradient-to-br from-red-900/30 to-red-600/20 backdrop-blur-md rounded-2xl p-6 border border-red-500/30 hover:border-red-400/60 transition-all duration-300 transform hover:-translate-y-2 hover:shadow-2xl hover:shadow-red-500/30">
            <div class="flex items-center mb-4">
              <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center mr-3 animate-pulse">
                <i class="fas fa-drumstick-bite text-white text-xl"></i>
              </div>
              <h3 class="text-2xl font-bold text-red-300">Imported Beef</h3>
            </div>
            <ul class="space-y-2 text-gray-200">
              <li class="flex items-start hover:text-red-300 transition">
                <i class="fas fa-chevron-right text-red-400 mt-1 mr-2 text-xs"></i>
                <span>Korean Souy Garlic</span>
              </li>
              <li class="flex items-start hover:text-red-300 transition">
                <i class="fas fa-chevron-right text-red-400 mt-1 mr-2 text-xs"></i>
                <span>Spicy Bulgogi</span>
              </li>
              <li class="flex items-start hover:text-red-300 transition">
                <i class="fas fa-chevron-right text-red-400 mt-1 mr-2 text-xs"></i>
                <span>Galbi Bulgogi</span>
              </li>
              <li class="flex items-start hover:text-red-300 transition">
                <i class="fas fa-chevron-right text-red-400 mt-1 mr-2 text-xs"></i>
                <span>Yangnyeom</span>
              </li>
              <li class="flex items-start hover:text-red-300 transition">
                <i class="fas fa-chevron-right text-red-400 mt-1 mr-2 text-xs"></i>
                <span>Pork Enoki</span>
              </li>
              <li class="flex items-start hover:text-red-300 transition">
                <i class="fas fa-chevron-right text-red-400 mt-1 mr-2 text-xs"></i>
                <span>Beef Enoki</span>
              </li>
              <li class="flex items-start hover:text-red-300 transition">
                <i class="fas fa-chevron-right text-red-400 mt-1 mr-2 text-xs"></i>
                <span>Barbecue</span>
              </li>
              <li class="flex items-start hover:text-red-300 transition">
                <i class="fas fa-chevron-right text-red-400 mt-1 mr-2 text-xs"></i>
                <span>Imported Plain Beef Roll</span>
              </li>
              <li class="flex items-start hover:text-red-300 transition">
                <i class="fas fa-chevron-right text-red-400 mt-1 mr-2 text-xs"></i>
                <span>BBQ Jack Daniels (no alcohol)</span>
              </li>
              <li class="flex items-start hover:text-red-300 transition">
                <i class="fas fa-chevron-right text-red-400 mt-1 mr-2 text-xs"></i>
                <span>Gochujang</span>
              </li>
            </ul>
          </div>

          <!-- Non-Pork -->
          <div class="menu-detail-card bg-gradient-to-br from-yellow-900/30 to-yellow-600/20 backdrop-blur-md rounded-2xl p-6 border border-yellow-500/30 hover:border-yellow-400/60 transition-all duration-300 transform hover:-translate-y-2 hover:shadow-2xl hover:shadow-yellow-500/30">
            <div class="flex items-center mb-4">
              <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center mr-3 animate-pulse">
                <i class="fas fa-egg text-white text-xl"></i>
              </div>
              <h3 class="text-2xl font-bold text-yellow-300">Non-Pork Choices</h3>
            </div>
            <ul class="space-y-2 text-gray-200">
              <li class="flex items-start hover:text-yellow-300 transition">
                <i class="fas fa-chevron-right text-yellow-400 mt-1 mr-2 text-xs"></i>
                <span>Spicy Chicken</span>
              </li>
              <li class="flex items-start hover:text-yellow-300 transition">
                <i class="fas fa-chevron-right text-yellow-400 mt-1 mr-2 text-xs"></i>
                <span>Shrimp</span>
              </li>
              <li class="flex items-start hover:text-yellow-300 transition">
                <i class="fas fa-chevron-right text-yellow-400 mt-1 mr-2 text-xs"></i>
                <span>Plain Chicken</span>
              </li>
              <li class="flex items-start hover:text-yellow-300 transition">
                <i class="fas fa-chevron-right text-yellow-400 mt-1 mr-2 text-xs"></i>
                <span>Marinated Chicken</span>
              </li>
            </ul>
          </div>

          <!-- Rice -->
          <div class="menu-detail-card bg-gradient-to-br from-green-900/30 to-green-600/20 backdrop-blur-md rounded-2xl p-6 border border-green-500/30 hover:border-green-400/60 transition-all duration-300 transform hover:-translate-y-2 hover:shadow-2xl hover:shadow-green-500/30">
            <div class="flex items-center mb-4">
              <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mr-3 animate-pulse">
                <i class="fas fa-bowl-rice text-white text-xl"></i>
              </div>
              <h3 class="text-2xl font-bold text-green-300">Rice</h3>
            </div>
            <ul class="space-y-2 text-gray-200">
              <li class="flex items-start hover:text-green-300 transition">
                <i class="fas fa-chevron-right text-green-400 mt-1 mr-2 text-xs"></i>
                <span>Plain</span>
              </li>
              <li class="flex items-start hover:text-green-300 transition">
                <i class="fas fa-chevron-right text-green-400 mt-1 mr-2 text-xs"></i>
                <span>Kimchi Rice</span>
              </li>
              <li class="flex items-start hover:text-green-300 transition">
                <i class="fas fa-chevron-right text-green-400 mt-1 mr-2 text-xs"></i>
                <span>Bibimbap</span>
              </li>
            </ul>
          </div>

          <!-- Drinks & Dessert -->
          <div class="menu-detail-card bg-gradient-to-br from-blue-900/30 to-blue-600/20 backdrop-blur-md rounded-2xl p-6 border border-blue-500/30 hover:border-blue-400/60 transition-all duration-300 transform hover:-translate-y-2 hover:shadow-2xl hover:shadow-blue-500/30">
            <div class="flex items-center mb-4">
              <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mr-3 animate-pulse">
                <i class="fas fa-glass-water text-white text-xl"></i>
              </div>
              <h3 class="text-2xl font-bold text-blue-300">Drinks & Dessert</h3>
            </div>
            <div class="space-y-4">
              <div>
                <h4 class="text-blue-300 font-semibold mb-2">Drinks</h4>
                <ul class="space-y-2 text-gray-200">
                  <li class="flex items-start hover:text-blue-300 transition">
                    <i class="fas fa-chevron-right text-blue-400 mt-1 mr-2 text-xs"></i>
                    <span>Four Seasons Red Iced Tea</span>
                  </li>
                </ul>
              </div>
              <div class="pt-3 border-t border-blue-500/30">
                <div class="flex items-center mb-2">
                  <i class="fas fa-ice-cream text-3xl text-blue-300 mr-3"></i>
                  <h4 class="text-blue-300 font-bold text-xl">UNLIMITED ICE CREAM</h4>
                </div>
                <p class="text-gray-300 text-sm italic">🍦 Enjoy as much as you want!</p>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-16 px-6 bg-black/50 backdrop-blur-sm">
      <div class="max-w-6xl mx-auto">
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-8">About Jeongol Izakaya</h2>
        
        <div class="grid md:grid-cols-2 gap-8 mb-12">
          <div class="space-y-4">
            <h3 class="text-2xl font-semibold text-orange-400 mb-4">Our Story</h3>
            <p class="text-gray-200 leading-relaxed">
              Jeongol Izakaya brings together the best of Korean and Japanese culinary traditions in a warm, welcoming atmosphere. Founded with a passion for authentic Asian cuisine, we've become a beloved destination for food lovers seeking quality, flavor, and an unforgettable dining experience.
            </p>
            <p class="text-gray-200 leading-relaxed">
              Our name combines "Jeongol" (정골) - a traditional Korean hotpot, with "Izakaya" - a Japanese-style gastropub, perfectly representing our fusion approach to dining.
            </p>
          </div>
          
          <div class="space-y-4">
            <h3 class="text-2xl font-semibold text-orange-400 mb-4">What Makes Us Special</h3>
            <ul class="space-y-3 text-gray-200">
              <li class="flex items-start">
                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                <span>Premium quality meats and fresh ingredients sourced daily</span>
              </li>
              <li class="flex items-start">
                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                <span>Authentic Korean BBQ experience with traditional side dishes (banchan)</span>
              </li>
              <li class="flex items-start">
                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                <span>Cozy, intimate atmosphere perfect for gatherings and celebrations</span>
              </li>
              <li class="flex items-start">
                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                <span>Expert chefs with years of experience in Asian cuisine</span>
              </li>
              <li class="flex items-start">
                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                <span>Unique fusion dishes that blend Korean and Japanese flavors</span>
              </li>
            </ul>
          </div>
        </div>

        <div class="bg-white/10 backdrop-blur-md rounded-xl p-8 text-center">
          <h3 class="text-2xl font-semibold mb-4">Opening Hours</h3>
          <div class="grid sm:grid-cols-2 gap-4 max-w-2xl mx-auto text-gray-200">
            <div>
              <p class="font-semibold text-orange-400">Monday - Sunday</p>
              <p>11:30 AM - 10:00 PM</p>
            </div>
          </div>
          <p class="mt-6 text-sm text-gray-300">
            <i class="fas fa-phone mr-2"></i>For reservations and inquiries, please contact us through our social media or visit us directly
          </p>
        </div>
      </div>
    </section>

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
          <a href="#"
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
        <h2 class="text-lg font-bold text-black">Our Location</h2>
        <button class="text-gray-500 hover:text-gray-800 text-2xl" onclick="closeModal('locationModal')">&times;</button>
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
        <h2 class="text-lg font-bold text-black">Submit Feedback</h2>
        <button class="text-gray-500 hover:text-gray-800 text-2xl" onclick="closeModal('feedbackModal')">&times;</button>
      </div>
      <div class="p-4">
        <form action="{{ route('customer.feedback') }}" method="POST">
          @csrf
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

    document.getElementById('mobileMenuBtn').addEventListener('click', () => {
      const menu = document.getElementById('mobileMenu');
      const icon = document.querySelector('#mobileMenuBtn i');
      menu.classList.toggle('hidden');
      icon.classList.toggle('fa-bars');
      icon.classList.toggle('fa-times');
    });

    document.getElementById('openLocation').addEventListener('click', () => openModal('locationModal'));
    document.getElementById('navLocation').addEventListener('click', () => openModal('locationModal'));
    document.getElementById('mobileLocation').addEventListener('click', () => {
      openModal('locationModal');
      document.getElementById('mobileMenu').classList.add('hidden');
    });

    document.getElementById('openFeedback').addEventListener('click', () => openModal('feedbackModal'));
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
          
          if (this.getAttribute('href') === '#menu') {
            setTimeout(() => {
              document.querySelectorAll('.menu-card').forEach(card => {
                card.classList.add('menu-highlight');
                setTimeout(() => card.classList.remove('menu-highlight'), 3600);
              });
            }, 800);
          }
        }
      });
    });

    const observerOptions = {
      threshold: 0.2,
      rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          setTimeout(() => {
            entry.target.classList.add('floating');
          }, 1000);
        }
      });
    }, observerOptions);

    document.querySelectorAll('.fade-in-up').forEach(el => {
      observer.observe(el);
    });

    let isParallaxEnabled = window.innerWidth > 768;
    
    document.getElementById('menuContainer')?.addEventListener('mousemove', (e) => {
      if (!isParallaxEnabled) return;
      
      const cards = document.querySelectorAll('.menu-card-wrapper');
      cards.forEach(card => {
        const rect = card.getBoundingClientRect();
        const x = e.clientX - rect.left - rect.width / 2;
        const y = e.clientY - rect.top - rect.height / 2;
        
        const rotateX = (y / rect.height) * 10;
        const rotateY = -(x / rect.width) * 10;
        
        card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateZ(20px)`;
      });
    });

    document.getElementById('menuContainer')?.addEventListener('mouseleave', () => {
      if (!isParallaxEnabled) return;
      
      const cards = document.querySelectorAll('.menu-card-wrapper');
      cards.forEach(card => {
        card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) translateZ(0px)';
      });
    });

    window.addEventListener('resize', () => {
      isParallaxEnabled = window.innerWidth > 768;
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
        if (alert) setTimeout(() => {
          alert.classList.add('opacity-0');
          setTimeout(() => alert.remove(), 500);
        }, 2000);
      });
    });
  </script>

</body>
</html>