<!DOCTYPE html>
<html lang="en-GB" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="description" content="@yield('meta_description', 'Hotel Group Booking Management System')">
  <title>@yield('title', 'Dashboard') | Hotel Group Booking Management</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
  @stack('styles')
</head>
<body data-page="@yield('page', '')">
  @include('partials.loader')
  <div class="app-wrapper">
    @include('partials.sidebar')

    <div class="app-main">
      @include('partials.header')

      <main class="app-content">
        @include('partials.flash')

        @yield('content')
      </main>
    </div>
  </div>

  @include('partials.modals')
  @include('partials.toaster')

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('assets/js/pages.js') }}"></script>
  <script src="{{ asset('assets/js/app.js') }}"></script>
  @stack('scripts')
</body>
</html>
