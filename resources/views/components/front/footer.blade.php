<footer class="bg-white" id="lang">
    <div class="container mx-auto px-4">
      <div class="w-full flex flex-col md:flex-row py-6">
        <div class="flex-1 mb-0 text-black">
          <a class="text-pink-600 no-underline hover:no-underline font-bold text-2xl lg:text-4xl" href="#">
            <a class="toggleColour text-white no-underline hover:no-underline font-bold text-2xl lg:text-4xl" href="{{ route('front.index') }}">
                <img src="{{ asset('images/lady_logo.webp') }}" style="width:60px;height:77px;display:inline;" alt="">
              @lang('text.Lady Store')
            </a>
          </a>
        </div>
        <div class="flex-1 pt-5">
            <h1 class="text-gray-500 text-xl"> 2021 &copy;  <a href="">@lang('text.Lady Store')</a></h1>
        </div>
        <div class="flex-1 pt-4">
            <a href="{{ LaravelLocalization::getLocalizedURL('ar', null, [], true) }}" class="text-gray-500" >
                <img src="{{  asset('images/flags/arabic.png')  }}" alt="user-image" class="mr-2 d-inline" style="display:inline;width:20px;hei
                20px"> العربية
            </a>
            <br>

            <a href="{{ LaravelLocalization::getLocalizedURL('en', null, [], true) }}" class="text-gray-500">
                <img src="{{ asset('images/flags/us.jpg') }}" alt="user-image" class="mr-2 d-inline" height="12" style="display:inline;width:20px;hei
                20px"> English
            </a>

        </div>


      </div>
    </div>
  </footer>
