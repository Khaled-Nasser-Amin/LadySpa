@extends('admin.layouts.app')
@section('title',__('text.Login'))
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
<div class="account-pages mb-5" style="margin: 200px 0 0 0 ;">
        <div class="container">
            <div class="row justify-content-center">
                @if (session('status'))
                    <div class="alert alert-success mb-3 rounded-0" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                <div class="col-md-8 col-lg-6 col-xl-5" >
                    <div class="card" >
                       <x-general.authentication-card-logo />

                        <div class="card-body text-white bg-dark">
                            @include('admin.partials.errors')
                            <form action="{{route('login_super_admin')}}" method='post'>
                                @csrf
                                <div class="form-group">
                                    <input class="form-control mb-1" type="text" name='email' id="username" required="" value="{{old('email')}}" placeholder="{{__('text.Username')}}">
                                     <x-general.input-error for="email" />
                                </div>

                                <div class="form-group">
                                    <input class="form-control" type="password" name='password' required="" id="password" placeholder="{{__('text.Password')}}">
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox checkbox-success">
                                        <input type="checkbox" name="remember_me" class="custom-control-input" id="checkbox-signin" checked>
                                        <label class="custom-control-label" for="checkbox-signin">{{__('text.Remember me')}}</label>
                                    </div>
                                </div>

                                <div class="form-group text-center mt-4 pt-2">
                                    <div class="col-sm-12">
                                        <a href="{{route('viewForget')}}" class="text-white"><i class="fa fa-lock mr-1"></i> {{__('text.Forgot your password?')}}</a>
                                    </div>
                                </div>

                                <div class="form-group account-btn text-center mt-2">
                                    <div class="col-12">
                                        <button class="btn width-md btn-bordered btn-danger waves-effect waves-light" type="submit">{{__('text.Log In')}}</button>
                                    </div>
                                </div>
                            </form>

                        </div>
                        <!-- end card-body -->
                    </div>
                    <!-- end card -->
                </div>
                <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
@endsection

