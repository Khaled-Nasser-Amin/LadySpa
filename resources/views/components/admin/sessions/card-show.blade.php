@forelse($sessions as $session)
    <div class="col-sm-12 col-lg-4 col-md-6" wire:key="{{ $session->id }}">
        <div class="news-grid" >

            <div class="news-grid-image">

                <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active" style="height: 250px!important">
                            @if ($session->additions()->count() > 0)
                                <span class="badge badge-danger" style="position: absolute;right:0">{{__('text.Addition')}}</span>
                            @endif
                            <img src="{{$session->image}}" class="d-block w-100" alt="..." style="  background-size: cover;">
                        </div>
                            <div class="carousel-item" style="height: 250px!important">
                                @if ($session->additions()->count() > 0)
                                    <span class="badge badge-danger" style="position: absolute;right:0">{{__('text.Addition')}}</span>
                                @endif
                                @foreach ($session->images as $image)
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
                            <a class="dropdown-item" href="/admin/session-update/{{$session->id}}-{{$session->slug}}">{{__('text.Edit')}}</a>
                            <a class="dropdown-item" href="/admin/session-details/{{$session->id}}-{{$session->slug}}">{{__('text.Show')}}</a>
                            <button class="dropdown-item" type="button" wire:click="confirmDelete({{$session->id}})">{{__('text.Delete')}}</button>
                        </div>
                    </div>
                </div>

            </div>




            <div class="news-grid-txt">

                <div class="row justify-content-between align-items-center">
                    <h2>{{app()->getLocale() == 'ar' ?$session->name_ar:$session->name_en}}</h2>
                    <span><i class="mdi mdi-calendar" aria-hidden="true"></i> {{date('M d Y',strtotime($session->created_at))}}</span>
                </div>
                <div class="row justify-content-between align-items-center">
                    <h2>{{ $session->isActive == 1 ? __('text.Available For Sale') : __('text.Not Available For Sale')}}</h2>
                    <input wire:click.prevent="updateStatus({{ $session->id }})" type="checkbox" {{ $session->isActive == 1 ? "checked" : '' }}>
                </div>

                @if ($session->additions()->count() > 0)

                    <br><span class="text-pink"> {{__('text.Additions')}} </span>

                @endif

                <div class="table-responsive col" style="height: auto!important;max-height:200px;overflow-y:scroll">
                    <table class="table table-sm table-borderless mb-0">
                        <tbody>
                            @if ($session->additions()->count() > 0)
                                @forelse($session->additions as $row)

                                    <tr>
                                        <th class="pl-0 w-25" scope="row"><strong>@lang('text.Addition Name')</strong></th>
                                        <td>{{ app()->getLocale() == 'ar' ? $row->name_ar :$row->name_en }}</td>
                                        <th class="pl-0 w-25" scope="row"><strong>@lang('text.Price')</strong></th>
                                        <td><span class="text-muted">{{$row->price}} @lang('text.SAR')</span></td>

                                    </tr>

                                @empty

                                @endforelse


                            @endif

                        </tbody>
                    </table>
                </div>

                <ul>

                        <li><br>
                            @if (!$session->sale)
                            <span class="text-pink"> {{__('text.Internal price')}} </span>| <span class="text-muted">{{$session->price}} @lang('text.SAR')</span>
                            @else
                                <span class="text-pink"> {{__('text.Internal price')}} </span>| <span class="text-muted"><del>{{$session->price}}</del> {{$session->sale}} @lang('text.SAR')</span>
                            @endif
                        </li>
                        <li>
                            @if ($session->external_price > 0 && !$session->external_sale)
                            <span class="text-pink"> {{__('text.External Price')}} </span>| <span class="text-muted">{{$session->external_price}} @lang('text.SAR')</span>
                            @elseif($session->external_price > 0 && $session->external_sale > 0)
                                <span class="text-pink"> {{__('text.External Price')}} </span>| <span class="text-muted"><del>{{$session->external_price}}</del> {{$session->external_sale}} @lang('text.SAR')</span>
                            @endif
                        </li>

                </ul>

                @if($session->description_ar || $session->description_en)
                <span class="text-pink">{{__('text.Description')}}</span>
                <div class="slimscroll description_scroll mb-0">{{app()->getLocale() == 'ar' ?$session->description_ar:$session->description_en}}</div>
                @endif

                <button id="changeFeatured" wire:click.prevent="updateFeatured({{$session->id}})" class="btn btn-{{$session->featured == 0 ? "secondary":"primary"}} mt-3 btn-rounded btn-bordered waves-effect width-md waves-light text-white d-block mx-auto w-75">{{__('text.Featured')}} <i class="far fa-star"></i></button>


            </div>


        </div>

    </div>
@empty
    <h1 class='text-center flex-grow-1'>{{__('text.No Data Yet')}}</h1>
@endforelse
