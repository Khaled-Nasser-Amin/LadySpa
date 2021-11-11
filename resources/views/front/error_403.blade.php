@extends('admin.layouts.app')
@section('title',403)
@push('css')
<style>
    body {
    background: #f782a9;
    background: -webkit-linear-gradient(to right, #f782a9, #cecccc);
    background: linear-gradient(to right, #f782a9, #cecccd)
}
</style>

@endpush
@section('content')
    <div class="account-pages mb-5" style="margin: 200px 0 0 0 ;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5 text-center">

                    <h1 style="font-size:165px">403</h1>
                    <p style="font-size:25px">@lang('text.Oops, UNAUTHORIZED')</p>

                    <a href="{{ url()->previous() }}">@lang('text.Return Back')</a>

                </div>
                <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
@endsection
