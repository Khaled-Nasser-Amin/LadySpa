<!DOCTYPE html>
<html lang="{{ LaravelLocalization::getCurrentLocale() }}" dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}">
@include('admin.layouts.head')

<body >
    <nav class="row navbar">
        <div class="container">
            <a class="toggleColour text-white" style="font-size: 25px;font-weight:bold" href="{{ route('front.index') }}">
                <img src="{{ asset('images/logo.png') }}" style="width:60px;height:77px;display:inline;" alt="">
              @lang('text.Lady Spa')
            </a>
        </div>
    </nav>
<div id="wrapper">
    @yield('content')
    <!-- Page Content -->
</div>
<!-- App js -->
<script src="{{asset('js/app.min.js')}}"></script>
<script src="{{asset('js/vendor.min.js')}}"></script>
@stack('js')
</body>
</html>
