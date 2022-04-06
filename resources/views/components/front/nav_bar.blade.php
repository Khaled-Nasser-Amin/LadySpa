  <!-- PRE LOADER -->

  <div class="preloader">
    <div class="sk-spinner sk-spinner-pulse"></div>
</div>



<!-- Navigation Section -->

<div class="navbar navbar-default navbar-fixed-top">
    <div class="container">

        <div class="navbar-header">
            <button class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon icon-bar"></span>
                <span class="icon icon-bar"></span>
                <span class="icon icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{ route('front.index') }}">
                <img src="{{ asset('images/logo.png') }}" style="width:60px;height:65px;display:inline;" alt="">
                <span>Lady</span> Spa
            </a>
        </div>

        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="#home" class="smoothScroll">@lang('text.Home')</a></li>
                <li><a href="#download" class="smoothScroll">@lang('text.Download')</a></li>
                <li><a href="#pricing" class="smoothScroll">@lang('text.Support')</a></li>
                <li><a href="#language">@lang('text.Language')</a></li>
                <li><a href="#contactus" data-toggle="modal" data-target="#modal1">@lang('text.Contact Us')</a></li>
                <li><a href="{{ route('admin.index') }}" class="wow section-btn btn btn-success smoothScroll" style="margin-top:0px;text:white">@lang('text.Login')</a></li>
            </ul>
        </div>

    </div>
</div>
