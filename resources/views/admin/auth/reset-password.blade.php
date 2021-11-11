@extends('admin.layouts.app')
@section('title',__('Recover Password'))
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
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card">

                        <div class="text-center account-logo-box" >
                            <div class="mt-2 mb-2">
                                <a class='btn btn-secondary waves-effect waves-light' rel="alternate" href="{{App::getLocale() == 'en' ? LaravelLocalization::getLocalizedURL('ar', null, [], true) :   LaravelLocalization::getLocalizedURL('en', null, [], true) }}">
                                    {{ app()->getLocale() == 'ar'? 'English' : 'العربية' }}
                                </a>
                            </div>
                        </div>

                        <div class="card-body text-white bg-dark">

                            <div class="text-center mb-4">
                                <p class="text-muted mb-0">@lang('text.New Password') </p>
                            </div>

                            <form action="{{route('changePassword')}}" method="post">
                                @csrf
                                <div class="form-group row">
                                    <div class="col-12">
                                        <input class="form-control" type="password" name="password" required="" placeholder="@lang('text.Password')">
                                        <x-general.input-error for="password" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-12">
                                        <input class="form-control" type="password" name="password_confirmation" required="" placeholder="@lang('text.Confirm Password')">
                                    </div>
                                </div>

                                <div class="form-group account-btn text-center mt-2 row">
                                    <div class="col-12">
                                        <button class="btn width-md btn-bordered btn-danger waves-effect waves-light" type="submit">
                                            @lang('text.Confirm')
                                        </button>
                                    </div>
                                </div>
                            </form>

                        </div>
                        <!-- end card-body -->
                    </div>
                    <!-- end card -->

                </div>

            </div>
            <!-- end col -->
        </div>
        <!-- end row -->
    </div>
@endsection
