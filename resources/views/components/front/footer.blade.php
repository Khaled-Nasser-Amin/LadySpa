 <!-- Footer Section -->

 <footer id="language">
    <div class="container">
        <div class="row">

            <div class="col-md-3 col-sm-3">
                <div class="wow fadeInUp footer-copyright" data-wow-delay="0.4s">
                    <p>2021 &copy;  <a href="">@lang('text.Lady Spa')</a></p>
                </div>
            </div>

            <div class="col-md-3 col-sm-3">
                <ul class="wow fadeInUp social-icon" data-wow-delay="0.8s">
                    <li>
                        <a href="{{ LaravelLocalization::getLocalizedURL('ar', null, [], true) }}" style="padding:10px 20px" class="text-gray-500" >
                            <img src="{{  asset('images/flags/arabic.png')  }}"  alt="user-image" class="mr-2 d-inline" style="display:inline;width:20px;hei
                            20px"> العربية
                        </a>
                    </li>
                    <br>

                    <li>
                        <a href="{{ LaravelLocalization::getLocalizedURL('en', null, [], true) }}" style="padding:10px 20px" class="text-gray-500">
                            <img src="{{ asset('images/flags/us.jpg') }}"  alt="user-image" class="mr-2 d-inline" height="12" style="display:inline;width:20px;hei
                            20px"> English
                        </a>
                    </li>
                </ul>
            </div>

            <div class="col-md-6 col-sm-6">
                <ul class="wow fadeInUp social-icon" data-wow-delay="0.8s">
                    <li style="width:100%">
                        <a href="{{route('front.terms')}}" class="text-black" style="padding:10px 20px">
                            @lang('text.Vendor terms and conditions')
                         </a>
                    </li>
                    <br>
                    <li style="width:100%">
                        <a href="{{route('front.user_terms')}}" class="text-black" style="padding:10px 20px">
                            @lang('text.User terms and conditions')
                         </a>
                    </li>
                    <br>
                    <li style="width:100%">
                        <a href="{{route('front.support')}}" class="text-black" style="padding:10px 20px">
                            @lang('text.Support')
                         </a>
                    </li>

                </ul>
            </div>

        </div>
    </div>
</footer>
