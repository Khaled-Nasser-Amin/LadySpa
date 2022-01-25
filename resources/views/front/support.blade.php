@extends('admin.layouts.app')
@section('title',__('text.Support'))
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
            @if (session('status'))
            <div class="alert alert-success mb-3 rounded-0 text-center" role="alert">
                {{ session('status') }}
            </div>
            @endif
            <div class="row justify-content-center">

                <div class="col-md-8 col-lg-6 col-xl-5" >
                    <div class="card" >
                       <x-general.authentication-card-logo />

                        <div class="card-body text-white bg-dark">
                            @include('admin.partials.errors')
                            <form action="{{route('front.support_post')}}" method='post'>
                                @csrf
                                <div class="form-group">
                                    <input class="form-control mb-1" type="text" name='name' required="" value="{{old('name')}}" placeholder="{{__('text.Name')}}">
                                    <x-general.input-error for="name" />
                                </div>
                                <div class="form-group">
                                    <input class="form-control mb-1" type="email" name='email'  required="" value="{{old('email')}}" placeholder="{{__('text.Email')}}">
                                    <x-general.input-error for="email" />
                                </div>


                                <div class="form-group">
                                    <textarea class="form-control mb-1" id="w3review" name="message" required=""   placeholder="{{__('text.Message')}}" ></textarea>
                                    <x-general.input-error for="message" />
                                </div>



                                <div class="form-group account-btn text-center mt-2">
                                    <div class="col-12">
                                        <button class="btn width-md btn-bordered btn-danger waves-effect waves-light" type="submit">{{__('text.Send')}}</button>
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

