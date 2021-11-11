<!DOCTYPE html>
<html lang="{{ LaravelLocalization::getCurrentLocale() }}" dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}">
@include('admin.layouts.head')
<body class="hold-transition sidebar-mini layout-fixed">

<div id="wrapper">

    @include('admin.partials.navbar')
    @include('admin.partials.aside')
    <div class="content-page">
        @include('admin.partials.success')
        @include('admin.partials.errors')
        @yield('content')
    </div>
    @include('admin.layouts.footer')
</div>
<script src="{{asset('js/vendor.min.js')}}"></script>
@stack('script')

<script src="{{asset('js/app.min.js')}}"></script>
</body>
</html>
