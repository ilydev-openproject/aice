<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect" />
  <link as="style"
    href="https://fonts.googleapis.com/css2?display=swap&amp;family=Noto+Sans%3Awght%40400%3B500%3B700%3B900&amp;family=Plus+Jakarta+Sans%3Awght%40400%3B500%3B700%3B800"
    onload="this.rel='stylesheet'" rel="preload" />
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
  <title>Stitch Design</title>
  <link href="data:image/x-icon;base64," rel="icon" type="image/x-icon" />

  @vite(['resources/css/app.css', 'resources/js/app.js'])
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
</head>

<body>
  <div class="inner-body bg-[#e7e7e7] max-w-sm mx-auto relative">
    {{ $slot }}
    <x-navigation></x-navigation>

  </div>
  <!-- Lucid Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>
  <script>
    lucide.createIcons();
  </script>
</body>

</html>