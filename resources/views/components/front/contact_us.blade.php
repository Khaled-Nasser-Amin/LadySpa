<!-- Pricing Section -->

<section id="pricing">
    <div class="container">
        <div class="row">

            <div class="col-md-12 col-sm-12">
                <div class="section-title">
                    <h1>@lang('text.Support')</h1>
                    <hr>
                </div>
            </div>

            @if ($setting->contact_email)
                <div class="wow fadeInUp col-md-3 col-sm-3" data-wow-delay="0.4s">
                    <div class="pricing-plan">
                        <div class="pricing-month">
                            <h2>@lang('text.Email')</h2>
                        </div>
                        <div class="pricing-title">
                            <h3>{{ $setting->contact_email }}</h3>
                        </div>
                    </div>
                </div>
            @endif
            @if ($setting->contact_phone)
                <div class="wow fadeInUp col-md-3 col-sm-3" data-wow-delay="0.6s">
                    <div class="pricing-plan">
                        <div class="pricing-month">
                            <h2>@lang('text.Phone')</h2>
                        </div>
                        <div class="pricing-title">
                            <h3>{{ $setting->contact_phone }}</h3>
                        </div>
                    </div>
                </div>
            @endif
            @if ($setting->contact_whatsapp)
                <div class="wow fadeInUp col-md-3 col-sm-3" data-wow-delay="0.8s">
                    <div class="pricing-plan">
                        <div class="pricing-month">
                            <h2>@lang('text.WhatsApp')</h2>
                        </div>
                        <div class="pricing-title">
                            <h3>{{ $setting->contact_whatsapp }}</h3>
                        </div>
                    </div>
                </div>
            @endif
            @if ($setting->contact_land_line)
                <div class="wow fadeInUp col-md-3 col-sm-3" data-wow-delay="1.0s">
                    <div class="pricing-plan">
                        <div class="pricing-month">
                            <h2>@lang('text.Land Line')</h2>
                        </div>
                        <div class="pricing-title">
                            <h3>{{ $setting->contact_land_line }}</h3>
                        </div>
                    </div>
                </div>
            @endif


        </div>
    </div>
</section>



