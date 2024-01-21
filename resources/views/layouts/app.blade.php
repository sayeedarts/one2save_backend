<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">
    <title>{{ $title ?? config('app.name', 'Laravel') }}</title>

    <!-- Simple bar CSS -->
    <link rel="stylesheet" href="{{ asset( path('admin') . 'css/simplebar.css') }}">
    <!-- Fonts CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Overpass:ital,wght@0,100;0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Icons CSS -->
    <link rel="stylesheet" href="{{ asset( path('admin') . '/css/feather.css') }}">
    <link rel="stylesheet" href="{{ asset( path('admin') . '/css/select2.css') }}">
    <link rel="stylesheet" href="{{ asset( path('admin') . '/css/dropzone.css') }}">
    <link rel="stylesheet" href="{{ asset( path('admin') . '/css/uppy.min.css') }}">
    <link rel="stylesheet" href="{{ asset( path('admin') . '/css/jquery.steps.css') }}">
    <link rel="stylesheet" href="{{ asset( path('admin') . '/css/jquery.timepicker.css') }}">
    <link rel="stylesheet" href="{{ asset( path('admin') . '/css/quill.snow.css') }}">
    <!-- Date Range Picker CSS -->
    <link rel="stylesheet" href="{{ asset( path('admin') . '/css/daterangepicker.css') }}">
    <!-- App CSS -->
    <link rel="stylesheet" href="{{ asset( path('admin') . '/css/app-light.css') }}" id="lightTheme">
    {{-- <link rel="stylesheet" href="{{ asset( path('admin') . '/css/app-dark.css') }}" id="darkTheme" disabled> --}}
    <style>
      option.select-active {
        background-color: brown !important;
    }
    </style>
  </head>
  <body class="vertical  light  ">
    <div class="wrapper">
      @include('admin.includes.topbar')
      @include('admin.includes.sidebar')
      <main role="main" class="main-content">
        @yield('content')
        {{-- Modals --}}
        @include('includes.notifications')
        @include('includes.shortcuts')
      </main> <!-- main -->
    </div> <!-- .wrapper -->

    {{-- @livewireScripts --}}
    {{-- <script src="{{ asset('js/app.js') }}"></script> --}}
    {{-- <script src="https://cdn.jsdelivr.net/gh/livewire/turbolinks@v0.1.x/dist/livewire-turbolinks.js" data-turbolinks-eval="false"></script> --}}

    <script src="{{ asset( path('admin') . '/js/jquery.min.js') }}"></script>
    <script src="{{ asset( path('admin') . '/js/popper.min.js') }}"></script>
    <script src="{{ asset( path('admin') . '/js/moment.min.js') }}"></script>
    <script src="{{ asset( path('admin') . '/js/bootstrap.min.js') }}"></script>
    <script>
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
    </script>
    @yield('scripts')
    
    <!-- <script src="{{ asset( path('admin') . '/js/apps.js') }}"></script> -->
    <script src="{{ asset( path('admin') . '/js/custom.js') }}"></script>
    <script>
      // Clear input value
      $(".clearme").on("click", function() {
          $(this).parent().find('input').val("");
      });

      $(document).ready(function() {
        $("img").on("error", function () {
            $(this).attr("src", "{{asset( path() . 'images/feature-2-3-1.png')}}");
        });
        $('#dataTable').DataTable();
      });
    </script>
  </body>
</html>