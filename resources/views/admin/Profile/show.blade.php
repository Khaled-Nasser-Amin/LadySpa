@extends('admin.layouts.appLogged')
@section('title','Profile')
@push('css')
    @livewireStyles
    <script type="module" src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js"></script>
    <link rel="stylesheet" href="{{asset('css/toast.style.min.css')}}">

@endpush
@section('content')
    <div>
        @livewire('admin.profile.update-profile-information-form')

        <x-general.section-border />

        @livewire('admin.profile.update-geo-location')

        <x-general.section-border />

        @livewire('admin.profile.update-password-form')


        <x-general.section-border />

        @livewire('admin.profile.two-factor-authentication-form')

        <x-general.section-border />

        @livewire('admin.profile.logout-other-browser-sessions-form')

        <x-general.section-border />
        @cannot('isAdmin')
            @livewire('admin.profile.delete-user-form')
        @endcannot

    </div>
@endsection
@push('script')
    @livewireScripts
    <script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBmX3cxNy7VH9WLrzoh6FLGkjtZ0g3tLSE
    &callback=initMap&libraries=&v=weekly"
    async
  ></script>
    <script>
        window.livewire.on('refresh-navbar',route=>{
            $.ajax({
                'url':route,
                'method':'get',
                success:function (result){
                    let navbar=$('.navRefresh');
                    navbar.empty();
                    navbar.html($('.navRefresh',result).html());
                }
            })
        })
    </script>

<script src="{{asset('js/toast.script.js')}}"></script>
<script>
    window.addEventListener('success',e=>{
        $.Toast(e.detail,"",'success',{
            stack: false,
            position_class: "toast-top-center",
            rtl: {{app()->getLocale()=='ar' ? "true" : 'false'}}
        });
    })
    window.addEventListener('danger',e=>{
        $.Toast(e.detail,"",'error',{
            stack: false,
            position_class: "toast-top-center",
            rtl: {{app()->getLocale()=='ar' ? "true" : 'false'}}
        });
    })


    //geo locationn button
    $(document).on('click','.custom-map-control-button',function(e){
        e.preventDefault();
    })
</script>





@endpush


