 <!-- Download Section -->

 <section id="download" style="margin: 50px 0;">
    <div class="container">
        <div class="row">

            <h1 class="wow fadeInUp text-center" data-wow-delay="0.4s">@lang('text.Download')</h1>
            <div class="" style="display: flex;justify-content:space-around;margin-top:50px;margin-bottom:50px">
                <div class="flex w-full w-5/6 sm:w-1/2 p-6 justify-center">
                    <a href="{{ $setting->ios_app_url == '' ? '#' : $setting->ios_app_url }}">
                        <img src="{{ asset('images/app_store.png') }}" alt="apple store"
                            style="height: 100px;width:270px">
                    </a>
                </div>
                <div class="flex w-full sm:w-1/2 p-6 justify-center	">
                    <a href="{{ $setting->android_app_url == '' ? '#' : $setting->android_app_url }}">
                        <img src="{{ asset('images/google_play.png') }}" alt="google play"
                            style="height: 100px;width:270px">
                    </a>

                </div>
            </div>


        </div>
    </div>
</section>
