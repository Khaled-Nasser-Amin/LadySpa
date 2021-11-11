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

                    <x-general.authentication-card-logo />

                    <div class="card-body text-white bg-dark">

                        <div class="text-center mb-4">

                            <p class="mb-0 text-white">{{__('text.Enter your email address and we\'ll send you an email with instructions to reset your password.')}} </p>
                        </div>

                        <form action="{{route('sendEmail')}}" method="post">
                            @csrf
                            <div class="form-group row">
                                <div class="col-12">
                                    <input class="form-control" name="email" type="email" required id="email" placeholder="{{__('text.Enter email')}}">
                                </div>
                                <x-general.input-error for="email" />
                            </div>
                            <div class="row mt-5">
                                <div class="col-sm-12 text-center">
                                    <p class="text-white">{{__('text.Already have account?')}} <a href="{{route('login')}}" class="text-pink ml-1 "><b>{{__('text.Log In')}}</b></a></p>
                                </div>
                            </div>
                            <div class="form-group account-btn text-center mt-2 row">
                                <div class="col-12">
                                    <button class="btn width-md btn-bordered btn-danger waves-effect waves-light" type="submit">{{__('text.Send')}}
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
