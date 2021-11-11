@section('title',__('text.Register'))
@push('css')
    @livewireStyles
    <link rel="stylesheet" href="{{asset('css/toast.style.min.css')}}">

    <style>
        body {
        background: #f782a9;
        background: -webkit-linear-gradient(to right, #f782a9, #cecccd);
        background: linear-gradient(to right, #f782a9, #cecccd)
        }
        .gm-style-iw-d{
            color:blue;
        }
    </style>
@endpush
    <div class="account-pages" style="margin: 220px 0 0 0 ;" >
        <div class="container ">
            <div class="row justify-content-center ">
                <div class="col-md-8 col-lg-6 col-xl-5" >

                    @if (!session()->has('activeCodeField'))
                        <div class="card" >
                            <x-general.authentication-card-logo />
                            <div class="card-body text-white bg-dark">
                                <form >
                                    <div class="row mb-4">
                                        <div class="form-group  col-sm-12" wire:ignore>
                                            <label for="map">{{__('text.Your Location')}}*</label>
                                            <div id="map" style="height:200px"></div>
                                        </div>
                                        <div class="form-group  col-sm-12">
                                            <x-general.input-error for="geoLocation" />
                                        </div>
                                        <div class="form-group col-md-6 col-sm-12">
                                            <label for="store_name">{{__('text.Store Name')}}*</label>
                                            <input id="store_name" type="text"  wire:model="store_name" class="form-control {{$errors->has('store_name') ? 'is-invalid' : ''}}">
                                            <x-general.input-error for="store_name" />
                                        </div>
                                        <div class="form-group col-md-6 col-sm-12">
                                            <label for="name">{{__('text.Name')}}*</label>
                                            <input type="text" id="name" wire:model="name" class="form-control {{$errors->has('name') ? 'is-invalid' : ''}}" placeholder="{{__('text.Full Name')}}*">
                                            <x-general.input-error for="name" />
                                        </div>
                                        <div class="form-group col-md-6 col-sm-12">
                                            <label for="phone">{{__('text.Phone Number')}}*</label>
                                            <input id="phone" type="text" wire:model="phone"  class="form-control {{$errors->has('phone') ? 'is-invalid' : ''}}">
                                            <x-general.input-error for="phone" />
                                        </div>
                                        <div class="form-group col-md-6 col-sm-12">
                                            <label for="whatsapp">{{__('text.WhatsApp')}}*</label>
                                            <input id="whatsapp" type="text" wire:model="whatsapp"  class="form-control {{$errors->has('whatsapp') ? 'is-invalid' : ''}}">
                                            <x-general.input-error for="whatsapp" />
                                        </div>
                                        <div class="form-group col-md-6 col-sm-12">
                                            <label for="email">{{__('text.Email')}}*</label>
                                            <input id="email" type="text" wire:model="email"  class="form-control {{$errors->has('email') ? 'is-invalid' : ''}}">
                                            <x-general.input-error for="email" />
                                        </div>

                                        <div class="form-group col-md-6 col-sm-12">
                                            <label for="location">{{__('text.Location')}}*</label>
                                            <input id="location" type="text" wire:model="location"  class="form-control {{$errors->has('location') ? 'is-invalid' : ''}}">
                                            <x-general.input-error for="location" />
                                        </div>

                                        <div class="row w-100 mx-0 px-0">
                                            <div class="form-group col-md-6 col-sm-12">
                                                <label for="pass">{{__('text.Password')}}*</label>
                                                <input id="pass" type="password" wire:model="password" class="form-control {{$errors->has('password') ? 'is-invalid' : ''}}"  placeholder="{{__('text.Password')}}">
                                                <x-general.input-error for="password" />
                                            </div>
                                            <div class="form-group col-md-6 col-sm-12">
                                                <label for="cfpass">{{__('text.Confirm Password')}}*</label>
                                                <input id="cfpass" type="password" wire:model="password_confirmation"   class="form-control {{$errors->has('password_confirmation') ? 'is-invalid' : ''}}" placeholder="{{__('text.Confirm Password')}}">
                                                <x-general.input-error for="password_confirmation" />
                                            </div>
                                        </div>

                                    </div>

                                    <div class="form-group account-btn text-center mt-2">
                                        <div class="col-12">
                                            <button wire:click.prevent="store" class="btn width-md btn-bordered btn-danger waves-effect waves-light" type="submit"  wire:loading.attr="disabled" >{{__('text.Register')}}</button>
                                        </div>
                                    </div>
                                </form>

                            </div>
                            <!-- end card-body -->
                        </div>
                        <!-- end card -->
                    @else
                    <div class="card" >
                        <x-general.authentication-card-logo />
                        <div class="card-body text-white bg-dark">
                            <form   >
                                <div class="row form-group">
                                    <div class=" w-100 row justify-content-between align-items-center">
                                        <h3 class="form-title text-white px-3">{{__('text.Account Confirmation')}}</h3>
                                        <i class="hover-white-text mdi mdi-close-circle" wire:click.prevent="cancel" style="color:#e6508a;"></i>
                                    </div>
                                    <h4 class="form-subtitle text-white w-100 px-3">{{ __("text.We have sent a verification code to your email")}} : {{session()->has('data.email') ? session()->get('data.email') : ''}}</h4>

                                    <p  class=" px-3" wire:ignore>
                                        <span id="text" class="font-14">{{__('text.Code will expire after : ')}}</span>
                                        <span id="timerCount" class="font-weight-bold" style="color: #e6508a;"></span>
                                    </p>
                                    <script>
                                        let time={{session()->get('time')}};
                                            time=parseInt(time)+(5*60);
                                            let x= setInterval(function (){
                                                var now = new Date().getTime()/1000;
                                                var distance = time - now;
                                                var minutes = Math.floor((distance % ( 60 * 60)) / ( 60));
                                                var seconds = Math.floor((distance % (60)));
                                                $('#timerCount').html(minutes+':'+seconds)
                                                if (distance < 0) {
                                                    clearInterval(x);
                                                    $('#timerCount').empty()
                                                    $('#text').addClass(['text-danger','font-weight-bold'])
                                                    $('#text').html("{{__('text.CODE EXPIRED')}}");
                                                }
                                            },1000)

                                        window.addEventListener('refreshCode',function (e){
                                            $('#text').removeClass(['text-danger','font-weight-bold'])
                                            $('#text').html("{{__('text.Code will expire after : ')}}")
                                            time=parseInt(e.detail)+(5*60)
                                            let x= setInterval(function (){
                                                var now = new Date().getTime()/1000;
                                                var distance = time - now;
                                                var minutes = Math.floor((distance % ( 60 * 60)) / ( 60));
                                                var seconds = Math.floor((distance % (60)));
                                                $('#timerCount').html(minutes+':'+seconds)
                                                if (distance < 0) {
                                                    clearInterval(x);
                                                    $('#timerCount').empty()
                                                    $('#text').addClass(['text-danger','font-weight-bold'])
                                                    $('#text').html("{{__('text.CODE EXPIRED')}}");

                                                }
                                            },1000)

                                        })

                                    </script>
                                </div>
                                <div class="form-group">
                                    <label for="frm-reg-lname">{{__('text.Code')}}*</label>
                                    <div class="row px-2">
                                        <input type="text" wire:model="code" class="col-8 form-control  {{$errors->has('code') ? 'is-invalid' : ''}}" placeholder="######">
                                        <a href="#" wire:click.prevent="resend" class="hover-dark-text col-4 row justify-content-center align-items-center" style="color: #e6508a;;">{{__('text.Resend?')}}</a>
                                    </div>

                                    <x-general.input-error for="code" />
                                </div>
                                <div class="form-group account-btn text-center mt-2">
                                    <div class="col-12">
                                        <button wire:click.prevent="create" class="btn width-md btn-bordered btn-danger waves-effect waves-light" type="submit"  wire:loading.attr="disabled" >{{__('text.Register')}}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif

                </div>
                <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
@push('js')
    @livewireScripts
    <script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBmX3cxNy7VH9WLrzoh6FLGkjtZ0g3tLSE
    &callback=initMap&libraries=&v=weekly"
    async
  ></script>

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




        //google map


        // Note: This example requires that you consent to location sharing when
        // prompted by your browser. If you see the error "The Geolocation service
        // failed.", it means you probably did not give permission for the browser to
        // locate you.
        let map, infoWindow;

        function initMap() {
        map = new google.maps.Map(document.getElementById("map"), {
            center: { lat: -34.397, lng: 150.644 },
            zoom: 12,
        });
        infoWindow = new google.maps.InfoWindow();
        const locationButton = document.createElement("button");
        locationButton.textContent = "@lang('text.Determine your location')";
        locationButton.classList.add("custom-map-control-button",'btn' , 'btn-primary','mb-3');
        map.controls[google.maps.ControlPosition.BOTTOM_CENTER].push(locationButton);
        locationButton.addEventListener("click", () => {
            // Try HTML5 geolocation.
            if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                const pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                };
                @this.set('geoLocation',position.coords.latitude+","+position.coords.longitude)
                infoWindow.setPosition(pos);
                infoWindow.setContent("@lang('text.Location found.')");
                infoWindow.open(map);
                map.setCenter(pos);
                },
                () => {
                handleLocationError(true, infoWindow, map.getCenter());
                }
            );
            } else {
            // Browser doesn't support Geolocation
            handleLocationError(false, infoWindow, map.getCenter());
            }
        });
        }

        function handleLocationError(browserHasGeolocation, infoWindow, pos) {
            infoWindow.setPosition(pos);
            infoWindow.setContent(
                browserHasGeolocation
                ? "Error: The Geolocation service failed."
                : "Error: Your browser doesn't support geolocation."
            );
            infoWindow.open(map);
        }

        $(document).on('click','.custom-map-control-button',function(e){
            e.preventDefault();
        })
    </script>
@endpush



