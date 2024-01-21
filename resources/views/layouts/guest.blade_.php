<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <meta name="csrf-token" content="{{ csrf_token() }}">

   <title>{{ config('app.name', 'Laravel') }}</title>

   <!-- Fonts -->
   <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

   <!-- Styles -->
   <link rel="stylesheet" href="{{ asset('css/app.css') }}">

   <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/pikaday/css/pikaday.css">

   @livewireStyles
</head>

<body>
   <div>
      <div class="page-wrapper ">
         @include('includes.header-top')
         @livewire('public-navigation')
         <div wire:loading.delay="200ms">
            Processing Payment...
         </div>
         {{ $slot }}
         <!--this is Footer Link -->
         <section class="site-footer">
            <div class="footer-top">
               <div class="thm-container">
                  <div class="widgets-section">
                     <div class="row">
                        <div class=" col-md-3 col-sm-4 col-xs-12">
                           <div class="footer-widget contact-widget">
                              <div class="title">
                                 <h3>About HMH</h3>
                              </div>
                              <span class="fa fa-map-marker"></span>
                              <span class="footer_address"> P.O Box 127104<br /> Jeddah 21352<br />
                                 Kingdom of Saudi Arabia</span><br />
                              <span class="fa fa-phone"></span>
                              <span class="footer_address">Phone: +966 12 215 2025 </span><br />
                              <span class="fa fa-envelope"></span>
                              <span class="footer_address">info@hmh-hospitals.com<br />
                              </span>
                           </div>
                        </div>
                        <div class=" col-md-3 col-sm-4 col-xs-12">
                           <div class="footer-widget">
                              <div class="title">
                                 <h3>Departments</h3>
                              </div>
                              <ul class="links-list">
                                 <li><a href="#"><i class="fa fa-angle-double-right"></i>Intensive Care Unit</a></li>
                                 <li><a href="#"><i class="fa fa-angle-double-right"></i>Internal Medicine</a></li>
                                 <li><a href="#"><i class="fa fa-angle-double-right"></i>Dental</a></li>
                                 <li><a href="#"><i class="fa fa-angle-double-right"></i>ENT</a></li>
                                 <li><a href="#"><i class="fa fa-angle-double-right"></i>Ophthalmology</a></li>
                                 <li><a href="#"><i class="fa fa-angle-double-right"></i>Obstetrics & Gynecology</a>
                                 </li>
                                 <li><a href="#"><i class="fa fa-angle-double-right"></i>Pediatrics</a></li>
                              </ul>
                           </div>
                        </div>
                        <div class=" col-md-3 col-sm-4 col-xs-12">
                           <div class="footer-widget">
                              <div class="title">
                                 <h3>Quick Links</h3>
                              </div>
                              <ul class="links-list">
                                 <li><a href="#"><i class="fa fa-angle-double-right"></i> About HMH</a></li>
                                 <li><a href="#"><i class="fa fa-angle-double-right"></i> Departments</a></li>
                                 <li><a href="#"><i class="fa fa-angle-double-right"></i> Our Doctors</a></li>
                                 <li><a href="#"><i class="fa fa-angle-double-right"></i>Request an Appointment</a></li>
                                 <li><a href="#"><i class="fa fa-angle-double-right"></i> Customer Care</a></li>
                                 <li><a href="#"><i class="fa fa-angle-double-right"></i> Latest News</a></li>
                              </ul>
                           </div>
                        </div>
                        <div class=" col-md-3 col-sm-4 col-xs-12">
                           <div class="footer-widget">
                              <div class="title">
                                 <h3>Download Now</h3>
                              </div>
                              <p><a href=""><img src="images/google-play.png" alt="" /></a></p>
                              <p><a href=""><img src="images/appstore.png" alt="" /></a></p>
                              <br />
                              <div class="social-icons">
                                 <p>
                                    <a target="_blank" href="http://facebook.com/"><i class="fa fa-facebook"></i></a>
                                    <a target="_blank" href="https://www.instagram.com/"><i
                                          class="fa fa-instagram"></i></a>
                                    <a target="_blank" href="https://twitter.com/"><i class="fa fa-twitter"></i></a>
                                    <a target="_blank" href="https://www.youtube.com/"><i class="fa fa-youtube"></i></a>
                                 </p>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="footer-bottom text-center">
               <div class="thm-container">
                  <p>Copyrights &copy; 2020 HMH Hospitals. All Rights Reserved. Powered By <a target="_blank"
                        href="http://amtechsa.com/">Amtech</a></p>
               </div>
            </div>
         </section>
      </div>
   </div>

   <!-- Scripts -->
   <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.7.3/dist/alpine.js" defer></script>

   <script src="{{ asset('js/jquery-2.1.4.min.js') }}"></script>
   <script src="{{ asset('js/functions.js') }}"></script>

   @livewireScripts
   <script src="{{ asset('js/app.js') }}"></script>
   <script src="https://cdn.jsdelivr.net/gh/livewire/turbolinks@v0.1.x/dist/livewire-turbolinks.js"
      data-turbolinks-eval="false"></script>
   <script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js"></script>


   {{-- @yeild('scripts') --}}
</body>

</html>