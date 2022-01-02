@props(['type','datetime','rooms','date','code'])
<div wire:ignore.self class="modal fade" id="modify_reservation" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg bg-primary">
    <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">@lang('text.Time')</h5>
        <button onclick="$('#modify_reservation').modal('toggle')" type="button" class="close"  >
            <span aria-hidden="true">&times;</span>
        </button>
        </div>
            <div class="modal-body d-flex flex-row flex-wrap">
                <div class="col-sm-12">
                    <h5>@lang('text.Reservation time'): <span style="font-size: 15px">{{ $datetime }}</span></h5>
                </div>

                <div class="col-sm-12">
                    <label for="date" class=" w-100" style="text-align: start!important">{{__('text.Date')}}</label><br>
                    <input type="date" class="form-control"  wire:model='date' id="date" ><br>
                    <x-general.input-error for="date" />
                </div>

                @foreach ($rooms as $room)
                @if ($type == 'indoor')
                <h4>@lang('text.Room') {{ $loop->index+1 }}</h4>
                @else
                <h4>@lang('text.Service provider') {{ $loop->index+1 }}</h4>
                @endif
                <div class="col-sm-12 my-3 d-flex flex-row flex-wrap justify-content-start">
                    @foreach ($room as $time)
                       @if (now()->format('Y-m-d H:i:s') < date('Y-m-d H:i:s',strtotime($date.explode('-',$time)[0])))
                            @php
                                $codeSelected=$loop->parent->index."".$loop->index;
                            @endphp
                            <button style="border-radius: 2rem" class="btn {{ $code == $codeSelected ? 'btn-secondary':'btn-info'}} col-md-4 col-sm-6 my-2"  wire:click.prevent="selectTime('{{ $codeSelected }}','{{ $time }}','{{ $loop->parent->index+1 }}')">{{ $time }}</button>
                        @endif
                    @endforeach
                </div>
                @endforeach

            </div>
        <div class="modal-footer row justify-content-center">
        <button wire:click.prevent="modifyReservation" style="border-radius: 2rem" type="button" class="btn btn-primary">@lang('text.Save')</button>
        </div>
    </div>
    </div>
</div>
