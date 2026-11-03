<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TEKSIKA</title>

  <script>
    if (sessionStorage.getItem('teksika_splash_seen')) {
      window.location.replace("{{ route('landingpageuser') }}");
    }
  </script>
  
  <!-- Google Fonts: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
</head>
<body data-target-url="{{ url('/landingpageuser') }}">

  <div class="loader-wrapper">
    <!-- Brand Logo -->
    <div class="logo-box">
      <img src="{{ asset('images/logo-putih.png') }}" alt="TEKSIKA" class="logo-img" />
    </div>

    <!-- Brand Name -->
    <h1 class="brand-name">TEKSIKA</h1>

    <!-- Minimalist Progress Bar -->
    <div class="progress-container">
      <div id="progressBar" class="progress-bar"></div>
    </div>

    <!-- Minimalist Percentage -->
    <div id="progressPercent" class="progress-percent">0%</div>
  </div>

  <script src="{{ asset('js/welcome.js') }}"></script>
</body>
</html>
