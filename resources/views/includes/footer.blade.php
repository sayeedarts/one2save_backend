<!--this is Footer Link -->
<section class="site-footer">
    <div class="footer-top">
       <div class="thm-container">
          <div class="widgets-section">
             <div class="row">
                <div class=" col-md-3 col-sm-4 col-xs-12">
                   <div class="footer-widget contact-widget">
                      <div class="title">
                         <h3> {{__('about')}} {{env('APP_NAME')}}</h3>
                      </div>
                     <span class="fa fa-map-marker"></span>
                        <span class="footer_address"> {!! $settings['address_' . app()->getLocale()] ?? ''  !!} </span><br />
                        <span class="fa fa-phone"></span>
                        <span class="footer_address">{{$settings['phone'] ?? '' }} </span><br />
                        <span class="fa fa-envelope"></span>
                        <span class="footer_address">{{$settings['company_email'] ?? '' }}<br />
                     </span>
                   </div>
                </div>
               @foreach ($settings['footer'] as $footer)
                  <div class=" col-md-3 col-sm-4 col-xs-12">
                     <div class="footer-widget">
                        <div class="title">
                           <h3>{{$footer['name_' . app()->getLocale()]}}</h3>
                        </div>
                        <div>
                           {!! $footer['content_' . app()->getLocale()] !!}
                        </div>
                     </div>
                  </div>
                @endforeach
                {{-- <div class=" col-md-3 col-sm-4 col-xs-12">
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
                </div> --}}
                {{-- {{dd($settings)}} --}}
                <div class=" col-md-3 col-sm-4 col-xs-12">
                   <div class="footer-widget">
                      <div class="title">
                         <h3> {{__('download_now')}} </h3>
                      </div>
                      <p>
                        <a href="{{$settings['android_app_link'] ?? 'javascript:void(0);'}}">
                           <img src="{{asset('public/images')}}/google-play.png" alt="" />
                        </a>
                     </p>
                     <p>
                        <a href="{{$settings['ios_app_link'] ?? 'javascript:void(0);'}}">
                           <img src="{{asset('public/images')}}/appstore.png" alt="" />
                        </a>
                     </p>
                      <br />
                      <div class="social-icons">
                         <p>
                            <a target="_blank" href="{{$settings['facebook'] ?? 'javascript:void(0);'}}"><i class="fa fa-facebook"></i></a>
                            <a target="_blank" href="{{$settings['instagram'] ?? 'javascript:void(0);'}}"><i class="fa fa-instagram"></i></a>
                            <a target="_blank" href="{{$settings['twitter'] ?? 'javascript:void(0);'}}"><i class="fa fa-twitter"></i></a>
                            <a target="_blank" href="{{$settings['youtube'] ?? 'javascript:void(0);'}}"><i class="fa fa-youtube"></i></a>
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
         {!! str_replace("[YEAR]", date('Y'), $settings['copyright_' . app()->getLocale()]) ?? 'Copyrights © ' . date('Y') !!}
      </div>
    </div>
 </section>