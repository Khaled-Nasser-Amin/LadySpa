<!-- Home Section -->

<section id="home" class="main">
    <div class="overlay"></div>
    <div class="container">
         <div class="row">

              <div class="wow fadeInUp col-md-6 col-sm-5 col-xs-10 col-xs-offset-1 col-sm-offset-0"
                   data-wow-delay="0.2s">
                   <img src="{{ asset('images/logo.png') }}" class="img-responsive" alt="Home">
              </div>

              <div class="col-md-6 col-sm-7 col-xs-12">
                   <div class="home-thumb">
                        <h1 class="wow fadeInUp" data-wow-delay="0.6s">@lang('text.About us')</h1>
                        <p class="wow fadeInUp" data-wow-delay="0.8s">
                            @lang('text.Offering a wide range of services able to satisfy every need and support each client during the exciting journey that will lead to the creation of a unique and perfect Spa.')
                        </p>
                        <a href="{{ route('front.register') }}" class="wow fadeInUp section-btn btn btn-success smoothScroll"
                             data-wow-delay="1s">
                              @lang('text.Join us as a Vendor')
                            </a>
                   </div>
              </div>

         </div>
    </div>
</section>
