@forelse($products as $product)
    <div class="col-sm-12 col-lg-4 col-md-6" wire:key="{{ $product->id }}">
        <div class="news-grid" >

            <div class="news-grid-image">

                <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active" style="height: 250px!important">
                            @if ($product->type == 'group')
                                <span class="badge badge-info" style="position: absolute;right:0">{{__('text.Group')}}</span>
                                @if ($product->group_sale > 0)
                                    <span class="badge badge-danger" style="position: absolute;">{{__('text.Sale')}}</span>

                                @endif
                            @endif
                            <img src="{{$product->image}}" class="d-block w-100" alt="..." style="  background-size: cover;">
                        </div>
                            <div class="carousel-item" style="height: 250px!important">
                                @if ($product->type == 'group')
                                <span class="badge badge-info" style="position: absolute;right:0">{{__('text.Group')}}</span>
                                @if ($product->group_sale > 0)
                                    <span class="badge badge-danger" style="position: absolute;">{{__('text.Sale')}}</span>

                                @endif
                            @endif
                                @foreach ($product->images as $image)
                                    <img src="{{$image->name}}" wire:key="{{ $image }}" class="img-fluid d-block w-100"  style="  background-size: cover;" alt="...">
                                @endforeach
                            </div>
                    </div>
                </div>
                <div class="news-grid-box" style="border-radius:50% ">
                    <div class="dropdown float-right">
                        <a href="#" class="dropdown-toggle card-drop arrow-none text-white" data-toggle="dropdown" aria-expanded="false">
                            <div><i class="mdi mdi-dots-horizontal h4 m-0 text-muted"></i></div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="/admin/products-update/{{$product->id}}-{{$product->slug}}">{{__('text.Edit')}}</a>
                            <a class="dropdown-item" href="/admin/product-details/{{$product->id}}-{{$product->slug}}">{{__('text.Show')}}</a>
                            <button class="dropdown-item" type="button" wire:click="confirmDelete({{$product->id}})">{{__('text.Delete')}}</button>
                        </div>
                    </div>
                </div>

            </div>




            <div class="news-grid-txt">

                <div class="row justify-content-between align-items-center">
                    <h2>{{app()->getLocale() == 'ar' ?$product->name_ar:$product->name_en}}</h2>
                    <span><i class="mdi mdi-calendar" aria-hidden="true"></i> {{date('M d Y',strtotime($product->created_at))}}</span>
                </div>
                <div class="row justify-content-between align-items-center">
                    <h2>{{ $product->isActive == 1 ? __('text.Available For Sale') : __('text.Not Available For Sale')}}</h2>
                    <input wire:click.prevent="updateStatus({{ $product->id }})" type="checkbox" {{ $product->isActive == 1 ? "checked" : '' }}>
                </div>

                @if ($product->type == 'single')
                <br><span class="text-pink"> {{__('text.Sizes')}} </span>
                @elseif($product->type == 'group')
                <br><span class="text-pink"> {{__('text.Products')}} </span>

                @endif

                <div class="table-responsive col" style="height: auto!important;max-height:200px;overflow-y:scroll">
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
                                @forelse ($product->child_products()->withTrashed()->get() as $child)
                                <tr>
                                    <th style="font-size: larger">
                                        {{ app()->getLocale() == 'ar' ? $child->name_ar : $child->name_en }}

                                        @if($child->deleted_at)
                                            <i class="mdi mdi-alert-decagram text-danger"></i>
                                        @endif

                                    </th>
                                </tr>
                                @forelse($child->pivot->sizes()->withTrashed()->get() as $row)

                                <tr>
                                    <th class="pl-0 w-25" scope="row"><strong>@lang('text.Size')
                                        @if($row->deleted_at)
                                            <i class="mdi mdi-alert-decagram text-danger"></i>
                                        @endif
                                        </strong></th>
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

                <ul>
                    @if ($product->type == "group")

                        <li><br>
                            @if (!$product->group_sale)
                            <span class="text-pink"> {{__('text.Price')}} </span>| <span class="text-muted">{{$product->group_price}} @lang('text.SAR')</span>
                            @else
                                <span class="text-pink"> {{__('text.Price')}} </span>| <span class="text-muted"><del>{{$product->group_price}}</del> {{$product->group_sale}} @lang('text.SAR')</span>
                            @endif
                        </li>
                    @endif

                </ul>

                @if($product->description_ar || $product->description_en)
                <span class="text-pink">{{__('text.Description')}}</span>
                <div class="slimscroll description_scroll mb-0">{{app()->getLocale() == 'ar' ?$product->description_ar:$product->description_en}}</div>
                @endif

                @if(!checkCollectionActive($product))
                    <button id="changeFeatured" wire:click.prevent="updateFeatured({{$product->id}})" class="btn btn-{{$product->featured == 0 ? "secondary":"primary"}} mt-3 btn-rounded btn-bordered waves-effect width-md waves-light text-white d-block mx-auto w-75">{{__('text.Featured')}} <i class="far fa-star"></i></button>
                @else
                <div class="alert alert-danger">@lang('text.In active collection becouse there are some data missing')</div>
                @endif

            </div>


        </div>

    </div>
@empty
    <h1 class='text-center flex-grow-1'>{{__('text.No Data Yet')}}</h1>
@endforelse
