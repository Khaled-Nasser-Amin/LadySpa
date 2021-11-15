<div wire:ignore.self class="modal fade"  id="groupOfProducts" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{__('text.Select Group of Products')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="addSelectProduct">
                    <div class="form-group">
                        <button type="button" wire:click.prevent="addProduct" class="btn btn-success btn_AddMore">
                            {{__('text.Add Product')}}</button>
                    </div>
                    @forelse($productsIndex as $index => $value)
                        <div class="form-group row justify-content-between">
                            <div class="col-md-3 col-sm-6">
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" wire:key="{{ $loop->index }}">
                                         @if (isset($value['product_id']) && !empty($value['product_id']))
                                            @php
                                                $product=App\Models\Product::find($value['product_id']);
                                            @endphp
                                            <img src="{{ $product->image }}" class="rounded-circle" style="width: 50px;height: 30px" alt="product-image">
                                            <span>{{app()->getLocale() == 'ar' ? $product->name_ar : $product->name_en}}</span>
                                        @else
                                            @lang('text.Select Product')
                                         @endif
                                    </button>

                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="height: 200px;overflow-y:auto;">
                                        @if ($products->count() > 0)
                                            @foreach ($products as $product )
                                            <a class="dropdown-item select_product" href="#"  data-index="{{ $index }}" data-product-id="{{ $product->id }}"  wire:key="{{ $loop->index }}">
                                                <img src="{{ $product->image }}" class="rounded-circle" style="width: 50px;height: 50px" alt="product-image">
                                                <span>{{app()->getLocale() == 'ar' ? $product->name_ar : $product->name_en}}</span>
                                            </a>
                                            @endforeach
                                        @else
                                        <span class="text-muted">@lang('text.No Data Yet')</span>
                                        @endif

                                    </div>

                                </div>
                                <x-general.input-error for="productsIndex.{{$index}}.product_id" />
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <select  wire:model='productsIndex.{{$index}}.size' class='form-control d-block'>
                                    <option value="">{{__('text.Select Size')}}</option>

                                    @forelse($productSizes[$index] as $size)
                                        @if ($size['stock'] > 0)
                                            <option value='{{$size['id']}}'>{{ $size['size']}}</option>
                                        @endif
                                    @empty
                                    @endforelse
                                </select>
                                <x-general.input-error for="productsIndex.{{$index}}.size" />
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <input type="text" wire:model="productsIndex.{{$index}}.quantity" placeholder="@lang('text.Quantity')" class="form-control d-block">
                                <x-general.input-error for="productsIndex.{{$index}}.quantity" />
                            </div>

                            <div class="col-md-2 col-sm-12">
                                <button type="button" wire:click="deleteProduct({{$index}})" class="btn btn-danger btn_remove">{{__("text.Delete")}}</button>
                            </div>
                        </div>
                    @empty
                    @endforelse

                </div>
            </div>
        </div>
    </div>

<script>
    $(document).on('click','.select_product',function(){
        let index=$(this).data('index');
        let id=$(this).data('product-id');
        window.Livewire.emit('selected_product',index,id)
        @this.set('productsIndex.'+index+'.product_id',id)

    })

</script>

</div>




