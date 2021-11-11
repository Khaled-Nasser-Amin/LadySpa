<form wire:submit.prevent="{{$action}}" >
    <div class="row">
        <div class="col-lg-6">
            <div class="p-4">
                <div class="form-group">
                    <label for="name_ar"> {{__('text.Name_ar')}}</label>
                    <input type="text" wire:model="name_ar" class="form-control" id="name_ar" name="name_ar">
                    <x-general.input-error for="name_ar" />
                </div>
                <div class="form-group">
                    <label for="name_en"> {{__('text.Name_en')}}</label>
                    <input type="text" wire:model="name_en" class="form-control" id="name_en" name="name_en">
                    <x-general.input-error for="name_en" />
                </div>
                {{-- <div class="form-group">
                    <label for="slug">{{__('text.Slug')}}</label>
                    <input type="text" name="slug" wire:model="slug" class="form-control" id="slug" >
                    <x-general.input-error for="slug" />
                </div> --}}


                <div class="form-group">
                    <label for="Description_ar">{{__('text.Description_ar')}}</label>
                    <textarea wire:model="description_ar" class="form-control" name="description_ar" id="Description_ar" rows="5"></textarea>
                    <x-general.input-error for="description_ar" />
                </div>
                <div class="form-group">
                    <label for="Description_en">{{__('text.Description_en')}}</label>
                    <textarea wire:model="description_en" class="form-control" name="description_en" id="Description_en" rows="5"></textarea>
                    <x-general.input-error for="description_en" />
                </div>
            </div>
        </div>

        <div class="col-lg-6 p-4" >

            <div class="form-group mb-4" >
                <label>{{__('text.Product Image')}} </label>
                <input type="file" wire:model="image"   class="form-control" data-height="210" />
            </div>
            <x-general.input-error for="image" />

            <div class="form-group mb-4" >
                <label>{{__('text.Product banner')}} </label>
                <input type="file" wire:model="product_banner"   class="form-control" data-height="210" />
            </div>
            <x-general.input-error for="product_banner" />
            <div class="form-group mb-4" >
                <label>{{__('text.Gallery')}}</label>
                <input type="file" wire:model="groupImage" class="form-control"  multiple data-height="210" />
            </div>
            <x-general.input-error for="groupImage" />

            <div class="form-group " wire:ignore wire:key="first">
                <label for="tax">{{__('text.Tax')}}</label>
                <select multiple="multiple" wire:model="taxes_selected" class="multi-select form-control border-secondary"  id="my_multi_select1"  data-plugin="multiselect">
                    @forelse ($taxes as $tax)
                        <option value='{{ $tax->id }}'   >{{ app()->getLocale() == 'ar' ? $tax->name_ar: $tax->name_en }} ({{  $tax->tax }}%)</option>
                    @empty
                    <option class="text-muted" disabled>@lang('text.No Data Yet')</option>
                    @endforelse
                </select>
            </div>

            <div class="mb-2">
                <x-general.input-error for="taxes_selected" />

            </div>


            <div class="form-group mx-2">
                <label class="mr-2"> {{__('text.Sizes and Prices')}}</label>
                <button type="button" class="btn btn-primary d-block" data-toggle="modal" data-target="#sizesAndPrices">@lang('text.Add price for each size')</button>
            </div>
            <x-general.input-error for="sizes" />
            <x-admin.products.modal-add-sizes :sizes="$sizes" :index="$index"/>
            <x-admin.products.modal-update-sizes :sizes="$sizes" :index="$index" />


        </div>

        <div class="text-center col-12">
            <button type="submit" class="btn btn-success waves-effect waves-light">{{__('text.Submit')}}</button>
        </div>
    </div>

</form>

@push('script')
    <script>
         $('#my_multi_select1').on('change',function(){
            $('#my_multi_select1').multiSelect('refresh');
            @this.set('taxes_selected',$(this).val());
        })

    </script>
@endpush
