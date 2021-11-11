@extends('admin.layouts.app')
@section('title',__('text.Reset Password'))
@push('css')
<style>
    body {
    background: #f782a9;
    background: -webkit-linear-gradient(to right, #f782a9, #cecccd);
    background: linear-gradient(to right, #f782a9, #cecccd)
}
</style>

@endpush
@section('content')
    <div class="account-pages" style="margin: 200px 0 0 0 ;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5  ">
                    <div class="card bg-dark">

                        <div class="card-body">

                            <div class="text-center">
                                <div class="checkmark mb-3">
                                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
                                        <circle class="path circle" fill="none" stroke="#4bd396" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1"/>
                                        <polyline class="path check" fill="none" stroke="#4bd396" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" points="100.2,40.2 51.5,88.8 29.8,67.5 "/>
                                    </svg>
                                </div>

                                <h4 class="text-white">@lang('text.We have sent Message To your Email address.')</h4>

                                <p class="text-white"> @lang('text.Please Check your email address') : {{ session()->get('email')?? null }}</p>
                            </div>

                        </div>
                        <!-- end card-body -->
                    </div>
                    <!-- end card -->

                    <div class="row mt-5">
                        <div class="col-sm-12 text-center">

                            <p class="text-muted">@lang('text.Return to') <a href="{{route('index')}}" class="text-primary mx-1">@lang('text.Log In')</a></p>
                        </div>
                    </div>

                </div>
                <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
@endsection
