@forelse($products as $product)
    <div class="col-sm-12 col-lg-4 col-md-6">
        <div class="news-grid">
            <div class="news-grid-image">
                <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active" >
                            <img src="{{$product->image}}" class="d-block w-100" alt="..." >
                        </div>
                        {{-- @foreach($product->colors as $color)
                            <div class="carousel-item">

                                @foreach ($color->images as $image)
                                    <img src="{{$image->name}}" class="img-fluid d-block w-100 h-100" alt="...">
                                @endforeach
                            </div>
                        @endforeach --}}
                    </div>
                </div>
                <div class="news-grid-box">
                    <div class="dropdown float-right">
                        <a href="#" class="dropdown-toggle card-drop arrow-none text-white" data-toggle="dropdown" aria-expanded="false">
                            <div><i class="mdi mdi-dots-horizontal h4 m-0 text-muted"></i></div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="/admin/products-update/{{$product->id}}-{{$product->slug}}">{{__('text.Edit')}}</a>
                            @
                            <a class="dropdown-item" href="/admin/product-details/{{$product->id}}-{{$product->slug}}">{{__('text.Show')}}</a>
                            <button class="dropdown-item" type="button" wire:click="confirmDelete({{$product->id}})">{{__('text.Delete')}}</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="news-grid-txt">
                    <div class="row justify-content-between align-items-center">
                        <h2>store name</h2>
                        <a href="{{$product->user->image}}" target="_blank"><img src="{{$product->user->image}}" class="rounded-circle" style="width: 50px;height: 50px" alt="user-image"></a>

                    </div>
                <div class="row justify-content-between align-items-center">
                    <h2>{{app()->getLocale() == 'ar' ?$product->name_ar:$product->name_en}}</h2>
                    <span><i class="mdi mdi-calendar" aria-hidden="true"></i> {{date('M d Y',strtotime($product->created_at))}}</span>
                </div>
                <div class="row justify-content-between align-items-center">
                    <h2>{{ $product->isActive == 1 ? __('text.Available For Sale') : __('text.Not Available For Sale')}}</h2>
                    <input wire:click.prevent="updateStatus({{ $product->id }})" type="checkbox" {{ $product->isActive == 1 ? "checked" : '' }}>
                </div>

                 <br><span class="text-pink"> {{__('text.Colors')}} </span>
                <ul class="" style="height: auto!important;max-height:200px;overflow-y:scroll">
                    {{-- @foreach($product->sizes as $row)
                        <li class="row  w-100 d-flex flex-column  ">
                            <div>
                                <span style="background-color: {{ $row->size }}; width:30px;height:30px;border-radius:50%;display:inline-block"></span>
                            </div>
                            <div>
                                 @if ($row->sale == 0 || $row->sale == null)
                                <span class="text-pink"> {{__('text.Price')}} </span>| <span class="text-muted">{{$row->price}} {{ app()->getLocale() == 'ar' ? 'ر.س' : 'SAR' }}</span>
                                @else
                                    <span class="text-pink"> {{__('text.Price')}} </span>| <span class="text-muted"><del>{{$row->price}}</del> {{$row->sale}} {{ app()->getLocale() == 'ar' ? 'ر.س' : 'SAR' }}</span>
                                @endif
                            </div>
                            <div>
                                <span class="text-pink"> ({{__('text.Size')}} , {{__('text.Quantity')}})</span>|
                                @foreach ($row->sizes as $size)
                                @if($size->stock == 0)
                                    <del  class="text-danger">({{$size->size}} , {{ $size->stock }})</del>
                                @else
                                    <span  class="text-muted">({{$size->size}} , {{ $size->stock }})</span>
                                @endif
                                @endforeach
                            </div>

                        </li>
                    @endforeach --}}
                </ul>
                <ul>
                    <li><br><span class="text-pink">{{__('text.Type Of Fabric')}}</span>
                        |<span class="text-muted">{{ $product->typeOfFabric }}</span>
                    </li>
                    <li><br><span class="text-pink">{{__('text.Type Of Sleeve')}}</span>
                        |<span class="text-muted">{{$product->typeOfSleeve}}</span>
                    </li>

                    <li><br><span class="text-pink">{{__('text.Category Name')}}</span>
                    |<span class="text-pink">
                        <a href="/admin/category/{{$product->category()->withTrashed()->first()->id}}-{{$product->category()->withTrashed()->first()->slug}}">{{app()->getLocale() == 'ar'? $product->category()->withTrashed()->first()->name_ar : $product->category()->withTrashed()->first()->name_en}}</a></span>
                    </li>
                </ul>

                @if($product->description_ar || $product->description_en)
                <span class="text-pink">{{__('text.Description')}}</span>
                <div class="slimscroll description_scroll mb-0">{{app()->getLocale() == 'ar' ?$product->description_ar:$product->description_en}}</div>
                @endif
                @if($product->additions)
                <span class="text-pink">{{__('text.Additions')}}</span> | <span class="text-muted">{{$product->additions}}</span><br>
                @endif

                @can('update',$product)
                <button id="changeFeatured" wire:click.prevent="updateFeatured({{$product->id}})" class="btn btn-{{$product->featured == 0 ? "secondary":"primary"}} mt-3 btn-rounded btn-bordered waves-effect width-md waves-light text-white d-block mx-auto w-75">{{__('text.Featured')}} <i class="far fa-star"></i></button>
                @endcan
                @can('isAdmin')
                <button id="changeSliderFeatured" wire:click.prevent="updateAdminFeatured({{$product->id}})" class="btn btn-{{$product->featured_slider == 0 ? "secondary":"primary"}} mt-3 btn-rounded btn-bordered waves-effect width-md waves-light text-white d-block mx-auto w-75">{{__('text.Featured slider')}} <i class="far fa-star"></i></button>
                @endcan
            </div>
        </div>
    </div>
@empty
    <h1 class='text-center flex-grow-1'>{{__('text.No Data Yet')}}</h1>
@endforelse
