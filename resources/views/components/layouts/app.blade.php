<!DOCTYPE html>
<html lang="en">

<head>
  <!-- PWA Manifest -->
  <link rel="manifest" href="{{ asset('manifest.json') }}">
  <meta name="theme-color" content="#ea2a33">

  <!-- iOS Support -->
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="apple-mobile-web-app-title" content="Sales Tracker">
  <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">

  <!-- head -->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect" />
  <link as="style"
    href="https://fonts.googleapis.com/css2?display=swap&amp;family=Noto+Sans%3Awght%40400%3B500%3B700%3B900&amp;family=Plus+Jakarta+Sans%3Awght%40400%3B500%3B700%3B800"
    onload="this.rel='stylesheet'" rel="preload" />
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
  <title>Aice Ilyas</title>
  <link href="data:image/x-icon;base64," rel="icon" type="image/x-icon" />

  <!-- Preload Font & Critical CSS -->
  <link rel="preload" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&display=swap"
    as="style" onload="this.onload=null;this.rel='stylesheet'">

  @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<style>
  :root {
    --brand-purple: #7A32D5;
    --brand-pink: #E500AB;
    --brand-red: #FF3C7B;
    --brand-yellow: #FFC24B;
  }

  .gradient-bg {
    background-image: linear-gradient(to right, var(--brand-pink), var(--brand-red), var(--brand-yellow));
  }
</style>

<body>
  <!-- From Uiverse.io by Nawsome -->
  <!-- <div id="loader"
    class="absolute w-full h-screen mx-auto flex flex-col items-center justify-center z-50 bg-white transition-opacity duration-500 ease-out">
    <div class="typewriter">
      <div class="slide"><i></i></div>
      <div class="paper"></div>
      <div class="keyboard"></div>
    </div>
  </div> -->
  <div class="inner-body bg-[#e7e7e7] max-w-sm mx-auto relative">
    {{ $slot }}
    <x-navigation></x-navigation>

  </div>
  <script>
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
          .then(registration => console.log('SW registered!'))
          .catch(error => console.log('SW registration failed:', error));
      });
    }
  </script>
  <!-- Lucid Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>
  <script>
    lucide.createIcons();
  </script>
  <!-- <script>
    document.addEventListener('DOMContentLoaded', () => {
      const loader = document.getElementById('loader');
      const innerBody = document.querySelector('.inner-body');

      window.addEventListener('load', () => {
        loader.style.opacity = '0';
        innerBody.style.opacity = '1';

        loader.addEventListener('transitionend', () => {
          loader.style.display = 'none';
        });
      });
    });
  </script> -->
</body>

</html>