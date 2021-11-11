
<head>
    <meta charset="utf-8" />
    <title class="mdi mdi-car-hatchback">@yield('title') | {{ __('text.Lady Spa')}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <meta content="@lang('Women\'s clothing online stores')" name="description" />
    <meta content="" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <link rel="icon" href="{{asset('images/icons/spa.png')}}" type="image/icon type">

    <style>
        .enlarged .left-side-menu #sidebar-menu>ul>li{
            margin-left: 2px;
            margin-right: 2px;
            overflow: hidden;
        }
        .enlarged .left-side-menu #sidebar-menu>ul>li:hover{
            overflow: initial;
        }
        #sidebar-menu>ul>li>a:active, #sidebar-menu>ul>li>a:focus, #sidebar-menu>ul>li>a:hover{
            color: #f06292!important;
        }
        #sidebar-menu>ul>li>a.active{
            color: #f06292!important;
        }

        .form-control,.bootstrap-tagsinput, .dropdown-menu{
            border: 1px solid #a1a6ab!important;
        }
        .navbar{
            border-bottom: rgba(252, 248, 248, 0.4) solid 1px;
            box-shadow:0 0 10px 0 rgba(0, 0, 0, 0.2)
        }

    </style>
    @stack('css')
    <!-- App css -->
    <link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" id="bootstrap-stylesheet" />
    <link href="{{asset('css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('css/app.min.css')}}" rel="stylesheet" type="text/css" id="app-stylesheet" />

    @if ( LaravelLocalization::getCurrentLocale() == 'ar')
        <link href="{{asset('css/app-rtl.min.css')}}" rel="stylesheet" type="text/css" />
    @endif

    <!-- Scripts -->
</head>
