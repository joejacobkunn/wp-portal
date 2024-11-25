
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="">
    <link rel="icon" href="/favicon.ico">
    <meta name="description" content="W&P Portal PWA" />
    <meta name="theme-color" media="(prefers-color-scheme: light)" content="#f3f3f3" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-title" content="W&P Portal PWA" />
    <meta name="apple-mobile-web-app-status-bar-style" content="white" />
    <link rel="manifest" href="http://testaccount.wandpconnect.localhost/fortis/app/manifest.json" />

    <title>Fortis Assist</title>

    <!-- Bootstrap core CSS -->
    <link href="/assets/css/pwa.css" rel="stylesheet">
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css" integrity="sha512-9xKTRVabjVeZmc+GUW8GgSmcREDunMM+Dt/GrzchfN8tkwHizc5RP4Ok/MXFFy5rIjJjzhndFScTceq5e6GvVQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  </head>

  <body class="bg-light">
    
    <nav class="navbar navbar-expand-lg px-3 pwa-navbar">
      <a class="navbar-brand" href="#">
        <img class="d-block mx-auto" src="{{ accountLogo() }}" alt="" width="145">
      </a>
      <div class="collapse navbar-collapse" id="navbarNav">
        <span class="h4 navbar-text mb-0">
            @yield('page_title', 'Fortis Assist')
        </span>   
      </div>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
    </nav>

    <div class="user-info">
      <span class="navbar-text me-4 float-end  mt-2">
          <i class="fa fa-user"></i> {{ auth()->user()->name }} ({{ auth()->user()->sx_operator_id }})
      </span>
      <div class="float-start mt-2 ms-4"><small><i class="fas fa-map-marker-alt"></i> {{ auth()->user()->office_location }}</small></div>
    </div>
    <div class="container">

        @yield('content')
    </div>

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <x-livewire-alert::scripts />

    <footer class="footer">
        <div class="container">
          <span class="text-muted">&copy; 2024 W&P Portal</span>
        </div>
      </footer>
  </body>
</html>
