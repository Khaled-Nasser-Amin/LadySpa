<div wire:key="10">
    @if ($action == 'store')
        <x-admin.products.ask_single_or_group wire:key="2"/>

    @endif
    @if ($type == 'single')
            <x-admin.products.single_product :action="$action" :taxes="$taxes" :sizes="$sizes" :index="$index" wire:key="1" />
    @endif
    @if ($type == 'group')
    @endif

</div>
