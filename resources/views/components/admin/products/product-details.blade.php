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
                                <a href="{{ $image['name'] }}" target="_blanc">
                                    <img class="d-block w-100" src="{{ $image['name'] }}" >
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
                @can('isAdmin')
                    <div class="row justify-content-between align-items-center">
                        <h3><span class="text-pink"><strong>@lang('text.Store Name')</strong> | </span>{{ $product->user->store_name}}</h3>
                        <a href="{{$product->user->image}}" target="_blank"><img src="{{$product->user->image}}" class="rounded-circle" style="width: 50px;height: 50px" alt="user-image"></a>
                    </div>
                @endcan
                <div class="row justify-content-between align-items-center">
                    <h5><span class="text-pink"><strong>@lang('text.Product Name')</strong> | </span>{{app()->getLocale() == 'ar' ?$product->name_ar:$product->name_en}}</h5>
                    <span><i class="mdi mdi-calendar" aria-hidden="true"></i> {{date('M d Y',strtotime($product->created_at))}}</span>
                </div>
                @if( $product->description_ar )
                    <h6 class="pt-1"><span class="text-pink"><strong>@lang('text.Description')</strong> | </span>{{  $product->description_ar }}</h6>
                @elseif ($product->description_en)
                <h6 class="pt-1"><span class="text-pink"><strong>@lang('text.Description')</strong> | </span>{{  $product->description_en }}</h6>

                @endif

                <div class="table-responsive mx-0 px-0">
                    <table class="table table-sm table-borderless mb-0 text-center mx-0 px-0">
                        <thead>
                            <tr>
                                <th><span class="text-pink"><strong>@lang('text.Type Of Fabric')</strong></span></th>
                                <th><span class="text-pink"><strong>@lang('text.Type Of Sleeve')</strong></span></th>
                                <th><span class="text-pink"><strong>@lang('text.Category Name')</strong></span></th>
                                <th><span class="text-pink"><strong>@lang('text.Additions')</strong></span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{$product->typeOfFabric}}</td>
                                <td>{{$product->typeOfSleeve }}</td>

                                <td>
                                    <a  @can('isAdmin')
                                            href="/admin/category/{{$product->category()->withTrashed()->first()->id}}-{{$product->category()->withTrashed()->first()->slug}}"
                                        @endcan
                                            >{{app()->getLocale() == 'ar'? $product->category()->withTrashed()->first()->name_ar : $product->category()->withTrashed()->first()->name_en}}</a></td>
                                <td>{{ $product->additions}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <hr>
                @foreach ($product->colors as $row)
                    <div class="row">
                        <div class="table-responsive col">
                            <table class="table table-sm table-borderless mb-0 text-center">
                                <tbody>
                                <tr>
                                    <th class="pl-0 w-25" scope="row"><strong>@lang('text.Color')</strong></th>
                                    <td><span class="{{ $active_color == $row->id ? 'border border-info' : '' }}" wire:click="changeColor({{ $row->id }})"  style="cursor: pointer;background-color: {{ $row->color }}; width:30px;height:30px;border-radius:50%;display:inline-block;border-width:4px!important;"></span></td>
                                </tr>
                                <tr>
                                    <th class="pl-0 w-25" scope="row"><strong>@lang('text.Price')</strong></th>
                                    <td>{{ $row->price }}</td>
                                </tr>
                                <tr>
                                    <th class="pl-0 w-25" scope="row"><strong>@lang('text.Sale')</strong></th>
                                    <td>{{ $row->sale == 0 ? __('text.No Sale') : $row->sale }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="table-responsive col">
                            <table class="table table-sm table-borderless mb-0">
                                <tbody>
                                    @foreach ($row->sizes as $size)
                                        <tr>
                                            <th class="pl-0 w-25" scope="row"><strong>@lang('text.Size')</strong></th>
                                            <td>{{ $size->size }}</td>
                                            <th class="pl-0 w-25" scope="row"><strong>@lang('text.Stock')</strong></th>
                                            <td>
                                                @if($size->stock != 0)
                                                {{  $size->stock }}

                                                    @else
                                                    <del class="text-danger">0</del>

                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <hr>
                @endforeach

                <div class="row mb-2">


                    <h4 class="text-pink">{{__('text.Taxes')}}</h4>
                    <table class="table table-sm table-borderless mb-0">
                        <tbody>
                            @foreach ($product->taxes as $tax)
                                <tr>
                                    <th class="pl-0 w-25" scope="row"><strong>@lang('text.Tax\'s Name')</strong></th>
                                    <td>{{ app()->getLocale() == 'ar' ? $tax->name_ar :$tax->name_en }}</td>
                                    <th class="pl-0 w-25" scope="row"><strong>@lang('text.Tax\'s Cost')</strong></th>
                                    <td>
                                        {{ $tax->tax }}%
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </section>
</div>
<!-- end col -->
