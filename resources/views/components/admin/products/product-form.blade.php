<div>
    @if ($action == 'store')
        <x-admin.products.ask_single_or_group />

    @endif
    @if ($type == 'single')
            <x-admin.products.single_product :action="$action" :taxes="$taxes" :sizes="$sizes" :index="$index" />
    @endif
    @if ($type == 'group')
        <x-admin.products.group_of_products :action="$action" :taxes="$taxes" :sizes="$sizes" :index="$index"/>
        <x-admin.products.add_collection_of_group_model :productSizes="$product_sizes"  :productsIndex="$productsIndex"  :products="$products" :sizes="$sizes" />

    @endif



</div>
