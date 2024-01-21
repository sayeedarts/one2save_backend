<div>
    <!--Mobile Slide Menu-->
    <div id="slide-menu">
        <!--slide menu close action-->
        <a href="javascript:void(0)" class="slide-menu-icon-close" title="Close"><i class="fa fa-times"></i></a>
        <!-- ./slide menu close action-->
        <ul class="slide-navigation">
            @foreach ($publicMainMenu as $menu)
                <li class="{{ !empty($menu['submenu']) ? 'menu-item-has-children' : '' }}"><a href="{{$menu['link']}}">{{$menu['title']}}</a>
                @if (!empty($menu['submenu']))
                    <ul>
                        @foreach ($menu['submenu'] as $submenu)
                            <li><a href="{{$submenu['link']}}">{{$submenu['title']}}</a></li>
                        @endforeach 
                    </ul>
                @endif
            </li>
            @endforeach
        </ul>
     </div>
    
     <!-- Pre Header After Login -->
     <div class="pre-header-after-login">
        <div class="thm-container">
            @if (Auth::check())
            <p>
                {{__('welcome')}} 
                <a class="pre-header-menu-action" href="javascript:void(0);">
                    {{ Auth::user()->name }}<i class="fa fa-caret-down" aria-hidden="true"></i>
                </a>
            </p>
            @else 
            <p>
                <a href="{{ route('user.login') }}">{{__('login')}} </a> 
                &nbsp;I&nbsp; <a href="{{ route('register-patient') }}">{{__('register_now')}}</a>
            </p>
            @endif
            
            <div class="pre-header-menu">
                <ul>
                    <li>
                        <a href="{{ route('user.my-account') }}" class="topmenu"><i class="fa fa-user" aria-hidden="true"></i> My Account</a>
                    </li>
                    <li>
                        <a href="{{ route('user.logout') }}" class="topmenu"><i class="fa fa-sign-out" aria-hidden="true"></i> Logout</a>
                    </li>
                </ul>
          </div>
        </div>
    </div>
    <!-- Pre Header After Login -->
    <!-- Main Header-->
    <div class="top-header home-two">
        <div class="thm-container clearfix">
            {{-- Language Translator Switch --}}
            @if (app()->getLocale() == "en")
                <a class="lang-switch" href="{{ url('locale/ar') }}" title="Arabic">عربي</a>
            @else 
                <a class="lang-switch" href="{{ url('locale/en') }}" title="English">English</a>
            @endif
            <div class="logo pull-left">
                <a href="{{route('landing')}}">
                    <img src="{{ asset('public/uploads/profile/') }}/{{$settings['logo']}}" alt="Site Logo" title="{{$settings['company_name'] ?? ''}}">
                </a>
            </div>
            <div class="header-right-info pull-right">
                <div class="single-header-info">
                    <div class="icon-box">
                        <i class="fa fa-envelope"></i>
                    </div>
                    <div class="text-box">
                        <p> {{ __('email_us') }} <br> <span>{{$settings['company_email']}}</span></p>
                    </div>
                </div>
                <div class="single-header-info">
                    <div class="icon-box">
                        <i class="fa fa-phone"></i>
                    </div>
                    <div class="text-box">
                        <p> {{ __('call_us') }} <br> <span>{{$settings['phone']}}</span></p>
                    </div>
                </div>
                <div class="single-header-info">
                    <a href="javascript:void(0);" class="search-icon"><i class="clinmedix-icon-search"></i></a>
                    <div class="search-wrap">
                        {!! Form::open(['url' => route('public.search'), 'method' => 'GET']) !!}
                            <input class="search-field" name="q" type="text" placeholder="Search...">
                            <button class="search-button" type="submit">
                                <span><i class="clinmedix-icon-search"></i></span>
                            </button>
                        {{ Form::close() }}
                      <a href="javascript:void(0)" class="search-close" title="Close"><i class="fa fa-times"></i></a>
                    </div>
                </div>
            </div>
            <!--Slide menu action link-->
            <a title="Menu" href="javascript:void(0)" class="slide-menu-icon"><i class="fa fa-bars"></i></a>
            <!-- ./Slide menu action link-->
        </div>
    </div>
    {{-- Desktop Main Menu --}}
    <header class="header header-home-two">
        <nav class="navbar navbar-default header-navigation stricky">
            <div class="thm-container clearfix">
                <div id="main-nav-bar">
                <ul id="primary-menu" class="nav navbar-nav navigation-box">
                    @foreach ($publicMainMenu as $menu)
                        <li><a href="{{$menu['link']}}">{{$menu['title']}}</a>
                        @if (!empty($menu['submenu']))
                            <ul class="sub-menu">
                                @foreach ($menu['submenu'] as $submenu)
                                    <li><a href="{{$submenu['link']}}">{{$submenu['title']}}</a></li>
                                @endforeach 
                            </ul>
                        @endif
                    </li>
                    @endforeach
                </ul>
                </div>
                <div class="right-side-box">
                <a href="{{route('book-an-appointment')}}" class="book-appointment"><i class="fa fa-calendar-alt"></i> {{__('book_appointment')}} </a>
                </div>
            </div>
        </nav>
    </header>
</div>