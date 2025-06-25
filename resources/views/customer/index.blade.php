<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  
  <title>Jeongol Izakaya</title>

  <!-- Bootstrap + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  
  <style>
    html, body {
      height: 100%;
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #fefefe;
    }

    body {
      display: flex;
      flex-direction: column;
      text-align: center;
    }

    main {
      flex: 1;
    }

    header {
      padding: 1rem;
      font-size: 1.5rem;
    }

    header img {
      height: 45px;
    }

    .location-btn {
      margin-top: 10px;
    }

    .reserve-link {
      display: inline-block;
      margin: 1rem 0;
      padding: 15px 30px;
      background-color: #e32929;
      color: #fff;
      font-weight: bold;
      border-radius: 5px;
      font-size: 1rem;
      text-decoration: none;
      transition: background-color 0.3s;
    }

    .reserve-link:hover {
      background-color: #ff0000;
    }

    .image-gallery {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 15px;
      padding: 1rem 0;
    }

    .image-gallery img {
      width: 100%;
      max-width: 200px;
      height: auto;
      background-color: #ccc;
      border-radius: 5px;
      margin-bottom: 6%;
    }

    .feedback-btn {
      display: inline-block;
      margin: 2rem 0 1rem;
      padding: 10px 20px;
      background-color: #8f8f8f;
      color: #fff;
      font-weight: bold;
      border-radius: 5px;
      text-decoration: none;
      font-size: 1rem;
    }

    footer {
      background-color: #e60707;
      color: white;
      padding: .1rem 0;
    }

    footer a {
      color: white;
      margin: 0 10px;
      font-size: .9rem;
    }

    footer a:hover {
      color: #ddd;
    }

    @media (max-width: 768px) {
      header {
        font-size: 1.2rem;
      }

      .reserve-link,
      .feedback-btn {
        font-size: 0.95rem;
        padding: 12px 20px;
      }

      footer a {
        font-size: 1.1rem;
      }
    }

    @media (max-width: 480px) {
      header img {
        height: 35px;
      }

      .reserve-link,
      .feedback-btn {
        width: 90%;
        font-size: 0.9rem;
      }
    }
  </style>
</head>

<body>

  <main>
    <header>
      <div> <img src="/assets/spoon-and-fork.png" alt="Logo"> Welcome to <strong>Jeongol Izakaya</strong></div>
      <button type="button" class="btn btn-danger location-btn" data-bs-toggle="modal" data-bs-target="#locationModal">
        Location
      </button>
    </header>

    <a href="{{route('customer.place_reservation')}}" class="reserve-link">Reserve Now!</a>

    <div class="image-gallery">
      <img src="" alt="pic1">
      <img src="" alt="pic2">
      <img src="" alt="pic3">
    </div>

    <a href="#" class="feedback-btn" data-bs-toggle="modal" data-bs-target="#feedbackModal">Submit Feedback</a>
  </main>

  <!-- Location Modal -->
  <div class="modal fade" id="locationModal" tabindex="-1" aria-labelledby="locationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="locationModalLabel">Our Location</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-0">
          <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3964.2720320936146!2d124.85643821044029!3d6.487195323557341!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x32f819e81bfe3b73%3A0x9301afee65466c72!2sJeongol%20Palace!5e0!3m2!1sen!2sph!4v1750692520597!5m2!1sen!2sph"
            width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
          </iframe>
        </div>
      </div>
    </div>
  </div>

  <!-- Feedback Modal -->
  <div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="feedbackModalLabel">Submit Feedback</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form>
            <div class="mb-3">
              <input type="text" class="form-control" placeholder="Your feedback...">
            </div>
            <button type="submit" class="btn btn-danger w-30">Submit</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <footer>
    <div class="container">
      <p class="mb-2">Contact us</p>
      <div class="mb-3">
        <a href="#"><i class="bi bi-facebook"></i></a>
        <a href="#"><i class="bi bi-instagram"></i></a>
        <a href="#"><i class="bi bi-twitter"></i></a>
        <a href="mailto:info@jeongolizakaya.com"><i class="bi bi-envelope-fill"></i></a>
      </div>
      <p class="mb-0">© 2023 Jeongol Izakaya. All rights reserved.</p>
    </div>
  </footer>

  <!-- Bootstrap Bundle JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
