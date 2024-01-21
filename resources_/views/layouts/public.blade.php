{{-- 
   non-livewire
   publi page's layout file   
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <link href="{{asset('public/images')}}/favicon.png" rel="shortcut icon" type="image/vnd.microsoft.icon" />

   <title>{{ $title ?? config('app.name', 'Laravel') }}</title>
   
   <!-- Fonts -->
   <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
   <!-- Styles -->
   <link rel="stylesheet" href="{{ asset( path() . 'css/app.css') }}">
   <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/pikaday/css/pikaday.css">
   <link rel="stylesheet" href="{{ asset(path() . 'js/toastr/toastr.min.css') }}">
</head>

<body>
   <!--Preloader-->
   <div class="preloader">
      <div class="lds-ellipsis"><div></div><div></div><div></div></div>
   </div>
   <!--Preloader-->
   <div>
      <div class="page-wrapper ">
         <x-public-main-menu/>
         @yield('content')
         @include('includes.footer')
      </div>
   </div>
   <div class="scroll-to-top" style="display: block;"><span class="fa fa-angle-up"></span></div>
   <!-- Scripts -->
   {{-- <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.7.3/dist/alpine.js" defer></script> --}}

   <script src="{{ asset(path() . 'js/jquery-2.1.4.min.js') }}"></script>
   <script src="{{ asset(path() . 'js/functions.js') }}"></script>
   <script src="{{ asset(path() . 'js/toastr/toastr.min.js') }}"></script>

   {{-- @livewireScripts --}}
   <script src="{{ asset( path() . 'js/app.js') }}"></script>
   {{-- <script src="https://cdn.jsdelivr.net/gh/livewire/turbolinks@v0.1.x/dist/livewire-turbolinks.js" data-turbolinks-eval="false"></script> --}}
   <script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js"></script>
   <script src="{{ asset( path() . 'admin/js/custom.js') }}"></script>
   <script>
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      $(document).ready(function() {
         $("img").on("error", function () {
            $(this).attr("src", "{{asset( path() . 'images/no-image480x480.png')}}");
         });
      });
      function scrollToElement(ele) {
         $(window).scrollTop(ele.offset().top).scrollLeft(ele.offset().left);
      }
  </script>
   @yield('scripts')
</body>
</html>