
<!-- Topbar Start -->
<div class="navbar-custom" style="background-color: #36404e;">
    <ul class="list-unstyled topnav-menu float-right mb-0 navRefresh">

        <li class="dropdown notification-list">
            <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                <img src="{{auth()->user()->image}}" alt="user-image" class="rounded-circle" style="width: 50px; height: 50px">
                <span class="d-none d-sm-inline-block ml-1 text-white">{{ Auth::user()->name }}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                <!-- item-->
                <div class="dropdown-header noti-title">
                    <h6 class="text-overflow m-0">{{__('text.Welcome')}} !</h6>
                </div>

                <!-- item-->
                <a href="{{route('profile.show')}}" class="dropdown-item notify-item">
                    <i class="mdi mdi-account-outline"></i>
                    <span>{{__('text.Profile')}}</span>
                </a>

                <div class="dropdown-divider"></div>
                <!-- item-->
                <a href="#" onclick="event.preventDefault();document.getElementById('form-logout').submit()" class="dropdown-item notify-item">
                    <i class="mdi mdi-logout-variant"></i>
                    <span>{{__('text.Logout')}}</span>
                </a>
                <form method="POST" action="{{route('logout')}}"  id="form-logout">
                    @method('post')
                    @csrf
                </form>

            </div>
        </li>


    </ul>

    <!-- LOGO -->
    <div class="logo-box" style="background-color: #f8f8f8">
        <a href="{{route('admin.index')}}" class="logo text-center">
            <span class="logo-lg text-pink " style="font-size: 20px;font-weight:bold">
                Lady Spa
            </span>
            <span class="logo-sm">
                 <span class="logo-sm-text-dark text-pink">Lady</span>
            </span>
        </a>
    </div>

    <ul class="list-unstyled topnav-menu topnav-menu-left m-0">
        <li>
            <button class="button-menu-mobile waves-effect">
                <i class="mdi mdi-menu text-white"></i>
            </button>
        </li>

        <li class="dropdown d-none d-lg-block">

            <a class="nav-link dropdown-toggle mr-0 text-white" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                <img src="{{app()->getLocale() == 'ar' ? asset('images/flags/arabic.png') : asset('images/flags/us.jpg')}}" alt="user-image" class="mr-2" height="12"> <span class="align-middle">{{app()->getLocale() == 'en' ? 'English' :'العربية'}} <i class="mdi mdi-chevron-down"></i> </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <!-- item-->
                <a href="{{App::getLocale() == 'ar' ? LaravelLocalization::getLocalizedURL('en', null, [], true) :   LaravelLocalization::getLocalizedURL('ar', null, [], true) }}" class="dropdown-item">
                    <img src="{{app()->getLocale() == 'ar' ? asset('images/flags/us.jpg') : asset('images/flags/arabic.png')}}" alt="user-image" class="mr-2 d-inline" height="12">
                    {{app()->getLocale() == 'ar' ? 'English' :'العربية'}}
                </a>

            </div>
        </li>


    </ul>
</div>
<!-- end Topbar -->



