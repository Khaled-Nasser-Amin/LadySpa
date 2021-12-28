<div class="row">
    <div class="col-12 row">
        <div class="col-12">
            @include('admin.partials.success')

        </div>

        {{--search boxes--}}
        <div class="row">
            <x-admin.sessions.search-boxes />
        </div>
        @forelse($sessions as $session)
        <div class="col-sm-12 col-lg-4 col-md-6">
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


                </div>




                <div class="news-grid-txt">

                    <div class="row justify-content-between align-items-center">
                        <h2>{{app()->getLocale() == 'ar' ?$session->name_ar:$session->name_en}}</h2>
                        <span><i class="mdi mdi-calendar" aria-hidden="true"></i> {{date('M d Y',strtotime($session->created_at))}}</span>
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
                    <span class="text-pink"> {{__('text.Time')}} </span>| <span class="text-muted">{{$session->time}}</span><br>

                    @if($session->description_ar || $session->description_en)
                    <span class="text-pink">{{__('text.Description')}}</span>
                    <div class="slimscroll description_scroll mb-0">{{app()->getLocale() == 'ar' ?$session->description_ar:$session->description_en}}</div>
                    @endif

                    @can('delete',$session)
                        @if($session->user)
                        <button  wire:click.prevent="confirmRestore({{$session->id}})" class="btn btn-primary mt-3 btn-rounded btn-bordered waves-effect width-md waves-light text-white d-block mx-auto w-75">{{__('text.Restore')}}</button>

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
        {{ $sessions->links() }}
    </div>
</div>
