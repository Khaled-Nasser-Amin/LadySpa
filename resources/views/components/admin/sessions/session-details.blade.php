<div class="col-md-12">
    <!--Section: Block Content-->
    <section class="mb-5">

        <div class="row">
            <div class="col-md-6">
                <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators">
                        @foreach ($images as $image)
                        <li data-target="#carouselExampleIndicators" data-slide-to="{{ $loop->index }}" class="{{ $loop->index ==  0 ? 'active' : '' }}"></li>
                        @endforeach

                    </ol>
                    <div class="carousel-inner">
                        @foreach ($images as $image)
                            <div class="carousel-item {{ $loop->index ==  0 ? 'active' : '' }}">
                                <a href="{{ $image }}" target="_blanc">
                                    <img class="d-block w-100" src="{{ $image }}" >
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                      <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                      <span class="carousel-control-next-icon" aria-hidden="true"></span>
                      <span class="sr-only">Next</span>
                    </a>
                  </div>


            </div>

            <div class="col-md-6">

                <div class="d-flex flex-row justify-content-between align-items-center">
                    <h5><span class="text-pink"><strong>@lang('text.Session Name')</strong> | </span>{{app()->getLocale() == 'ar' ? $session->name_ar: $session->name_en}}</h5>
                    <span><i class="mdi mdi-calendar" aria-hidden="true"></i> {{date('M d Y',strtotime($session->created_at))}}</span>
                </div>
                @if( $session->description_ar )
                    <h6 class="pt-1"><span class="text-pink"><strong>@lang('text.Description')</strong> | </span>{{  $session->description_ar }}</h6>
                @elseif ($session->description_en)
                <h6 class="pt-1"><span class="text-pink"><strong>@lang('text.Description')</strong> | </span>{{  $session->description_en }}</h6>

                @endif

                <h6>
                    <br>
                    @if (!$session->sale)
                    <span class="text-pink"> {{__('text.Internal price')}} </span>| <span class="text-muted">{{$session->price}} @lang('text.SAR')</span>
                    @else
                        <span class="text-pink"> {{__('text.Internal price')}} </span>| <span class="text-muted"><del>{{$session->price}}</del> {{$session->sale}} @lang('text.SAR')</span>
                    @endif
                </h6>

                <h6>
                    @if ($session->external_price > 0 && !$session->external_sale)
                    <span class="text-pink"> {{__('text.External price')}} </span>| <span class="text-muted">{{$session->external_price}} @lang('text.SAR')</span>
                    @elseif($session->external_price > 0 && $session->external_sale > 0)
                        <span class="text-pink"> {{__('text.External price')}} </span>| <span class="text-muted"><del>{{$session->external_price}}</del> {{$session->external_sale}} @lang('text.SAR')</span>
                    @endif
                </h6>

                @if ($session->additions()->count() > 0)
                <br><span class="text-pink"> {{__('text.Additions')}} </span>
                @endif

                <div class="table-responsive col" >
                    <table class="table table-sm table-borderless mb-0">
                        <tbody>
                            @if ($session->additions()->count() > 0)
                                @forelse($session->additions as $row)

                                    <tr>
                                        <th class="pl-0 w-25" scope="row"><strong>@lang('text.Addition Name')</strong></th>
                                        <td>{{ app()->getLocale() == 'ar' ? $row->name_ar:$row->name_en }}</td>
                                        <th class="pl-0 w-25" scope="row"><strong>@lang('text.Price')</strong></th>
                                        <td><span class="text-muted">{{$row->price}} @lang('text.SAR')</span></td>

                                    </tr>

                                @empty

                                @endforelse


                            @endif

                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </section>
</div>
<!-- end col -->
