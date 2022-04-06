<!DOCTYPE html>
<html lang="{{ LaravelLocalization::getCurrentLocale() }}"
    dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="icon" href="{{ asset('images/icons/spa.png') }}" type="image/icon type">

    <title>@lang('text.Lady Spa') | @lang('text.Home')</title>

    <link rel="stylesheet" href="{{ asset('css/landing_page/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/landing_page/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('css/landing_page/font-awesome.min.css') }}">

    <link rel="stylesheet" href="{{ asset('css/landing_page/magnific-popup.css') }}">

    <link href='https://fonts.googleapis.com/css?family=Unica+One' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,700' rel='stylesheet' type='text/css'>

    <!-- Main css -->
    <link rel="stylesheet" href="{{ asset('css/landing_page/style.css') }}">

</head>

<body data-spy="scroll" data-target=".navbar-collapse" data-offset="50">



    {{-- nav_bar --}}
    <x-front.nav_bar />


    <!--join_us-->
    <x-front.join_us />






    {{-- download --}}
    <x-front.download :setting="$setting" />




    <!--support-->
    <x-front.contact_us :setting="$setting" />






    <!--Footer-->
    <x-front.footer />





    <!--Modal contace-->
    <x-front.modal_contact />








    <!-- SCRIPTS -->

    <script src="{{ asset('js/landing_page/jquery.js') }}"></script>
    <script src="{{ asset('js/landing_page/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/landing_page/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('js/landing_page/magnific-popup-options.js') }}"></script>
    <script src="{{ asset('js/landing_page/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('js/landing_page/smoothscroll.js') }}"></script>
    <script src="{{ asset('js/landing_page/wow.min.js') }}"></script>
    <script src="{{ asset('js/landing_page/custom.js') }}"></script>

</body>

</html>
