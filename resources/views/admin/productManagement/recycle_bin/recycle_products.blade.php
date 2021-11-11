<div class="row">
    <div class="col-12 row">
        <div class="col-12">
                    @include('admin.partials.success')

        </div>

        {{--search boxes--}}
        <div class="row">
            <x-admin.products.search-boxes />
        </div>
        @forelse($products as $product)
        <div class="col-sm-12 col-lg-4 col-md-6">
            <div class="news-grid">
                <div class="news-grid-image">
                    <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item active" >
                                <img src="{{$product->image}}" class="d-block w-100" alt="..." >
                            </div>
                            @foreach($product->colors as $color)
                                <div class="carousel-item">

                                    @foreach ($color->images as $image)
                                        <img src="{{$image->name}}" class="img-fluid d-block w-100 h-100" alt="...">
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="news-grid-txt">
                    @can('isAdmin')
                        <div class="row justify-content-between align-items-center">
                            <h2>{{ $product->user()->withTrashed()->first()->store_name}}</h2>
                            <a href="{{$product->user()->withTrashed()->first()->image}}" target="_blank"><img src="{{$product->user()->withTrashed()->first()->image}}" class="rounded-circle" style="width: 50px;height: 50px" alt="user-image"></a>

                        </div>
                    @endcan
                    <div class="row justify-content-between align-items-center">
                        <h2>{{app()->getLocale() == 'ar' ?$product->name_ar:$product->name_en}}</h2>
                        <span><i class="mdi mdi-calendar" aria-hidden="true"></i> {{date('M d Y',strtotime($product->deleted_at))}}</span>
                    </div>

                    <br><span class="text-pink"> {{__('text.Colors')}} </span>
                    <ul class="" style="height: auto!important;max-height:200px;overflow-y:scroll">
                        @foreach($product->colors as $row)
                            <li class="row  w-100 d-flex flex-column  ">
                                <div>
                                    <span style="background-color: {{ $row->color }}; width:30px;height:30px;border-radius:50%;display:inline-block"></span>
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
                        @endforeach
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
                            <a @can('isAdmin')
                            href="/admin/category/{{$product->category()->withTrashed()->first()->id}}-{{$product->category()->withTrashed()->first()->slug}}"
                            @endcan
                            >{{app()->getLocale() == 'ar'? $product->category()->withTrashed()->first()->name_ar : $product->category()->withTrashed()->first()->name_en}}</a></span>
                        </li>
                    </ul>

                    @if($product->description_ar || $product->description_en)
                    <span class="text-pink">{{__('text.Description')}}</span>
                    <div class="slimscroll description_scroll mb-0">{{app()->getLocale() == 'ar' ?$product->description_ar:$product->description_en}}</div>
                    @endif
                    @if($product->additions)
                    <span class="text-pink">{{__('text.Additions')}}</span> | <span class="text-muted">{{$product->additions}}</span><br>
                    @endif

                    @can('delete',$product)
                        @if($product->user)
                        <button  wire:click.prevent="confirmRestore({{$product->id}})" class="btn btn-primary mt-3 btn-rounded btn-bordered waves-effect width-md waves-light text-white d-block mx-auto w-75">{{__('text.Restore')}}</button>

                        @endif
                    @endcan
                </div>
            </div>
        </div>
    @empty
        <h1 class='text-center flex-grow-1'>{{__('text.No Data Yet')}}</h1>
    @endforelse
    </div>
    <div class="col-12">
        {{ $products->links() }}
    </div>
</div>
