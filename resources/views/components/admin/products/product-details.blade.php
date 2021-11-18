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
                    <h5><span class="text-pink"><strong>@lang('text.Product Name')</strong> | </span>{{app()->getLocale() == 'ar' ?$product->name_ar:$product->name_en}}</h5>
                    <span><i class="mdi mdi-calendar" aria-hidden="true"></i> {{date('M d Y',strtotime($product->created_at))}}</span>
                </div>
                @if( $product->description_ar )
                    <h6 class="pt-1"><span class="text-pink"><strong>@lang('text.Description')</strong> | </span>{{  $product->description_ar }}</h6>
                @elseif ($product->description_en)
                <h6 class="pt-1"><span class="text-pink"><strong>@lang('text.Description')</strong> | </span>{{  $product->description_en }}</h6>

                @endif
                <h6 class="pt-1"><span class="text-pink"><strong>@lang('text.Type')</strong> | </span>{{  $product->type }}</h6>
                @if ($product->type == "group")
                <h6>
                    <br>
                    @if (!$product->group_sale)
                    <span class="text-pink"> {{__('text.Price')}} </span>| <span class="text-muted">{{$product->group_price}} @lang('text.SAR')</span>
                    @else
                        <span class="text-pink"> {{__('text.Price')}} </span>| <span class="text-muted"><del>{{$product->group_price}}</del> {{$product->group_sale}} @lang('text.SAR')</span>
                    @endif
                </h6>
                @endif

                @if ($product->type == 'single')
                <br><span class="text-pink"> {{__('text.Sizes')}} </span>
                @elseif($product->type == 'group')
                <br><span class="text-pink"> {{__('text.Products')}} </span>

                @endif

                <div class="table-responsive col" >
                    <table class="table table-sm table-borderless mb-0">
                        <tbody>
                            @if ($product->type == 'single')
                                @forelse($product->sizes as $row)

                                    <tr>
                                        <th class="pl-0 w-25" scope="row"><strong>@lang('text.Size')</strong></th>
                                        <td>{{ $row->size }}</td>
                                        <th class="pl-0 w-25" scope="row"><strong>@lang('text.Price')</strong></th>
                                        @if ($row->sale == 0 || $row->sale == null)
                                        <td><span class="text-muted">{{$row->price}} @lang('text.SAR')</span></td>
                                        @else
                                            <td><span class="text-muted"><del>{{$row->price}}</del> {{$row->sale}} @lang('text.SAR')</span></td>
                                        @endif
                                        <th class="pl-0 w-25" scope="row"><strong>@lang('text.Stock')</strong></th>
                                        <td>
                                            @if($row->stock != 0)
                                            {{  $row->stock }}

                                                @else
                                                <del class="text-danger">0</del>

                                            @endif
                                        </td>
                                    </tr>

                                @empty

                                @endforelse
                            @elseif($product->type == 'group')
                                @forelse ($product->child_products()->get() as $child)
                                <tr>
                                    <th style="font-size: larger">
                                        {{ app()->getLocale() == 'ar' ? $child->name_ar : $child->name_en }}

                                    </th>
                                </tr>
                                @forelse($child->pivot->sizes()->get() as $row)

                                <tr>
                                    <th class="pl-0 w-25" scope="row"><strong>@lang('text.Size')</strong></th>
                                    <td class="text-muted">{{ $row->size }}</td>
                                    <th class="pl-0 w-25" scope="row"><strong>@lang('text.Quantity')</strong></th>
                                    <td class="text-muted">{{ $row->pivot->quantity }}</td>

                                </tr>

                            @empty

                            @endforelse
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
