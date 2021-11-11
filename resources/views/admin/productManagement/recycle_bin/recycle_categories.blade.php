
<div class="card-box" style="overflow-y: scroll">
    @include('admin.partials.success')

    <input type="text" class="form-control col-md-4 col-sm-8 mb-4" placeholder="{{__('text.Search')}}..." wire:model="search">
    <table  class="table table-striped table-secondary" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
        <thead>
        <tr>
            <th>{{__('text.Image')}}</th>

            <th>{{__('text.Name_ar')}}</th>
            <th>{{__('text.Name_en')}}</th>
            <th>{{__('text.Slug')}}</th>
            <th>{{__('text.Parent Category')}}</th>
            <th>{{__('text.Action')}}</th>
        </tr>
        </thead>

        <tbody>
        @forelse($categories as $category)
            <tr>
                <td>
                    <a  href="/admin/category/{{$category->id}}-{{\Illuminate\Support\Str::slug($category->slug)}}">
                        <div class="inbox-item-img">
                            <img style="width: 50px;height: 50px" class="img-thumbnail" src="{{$category->image}}" alt="">
                        </div>
                    </a>
                </td>
                <td><span >{{$category->name_ar}}</span></td>
                <td><span >{{$category->name_en}}</span></td>
                <td><span>{{$category->slug}}</span></td>

                @if($category->parent_category)
                    <td>
                        <div class="inbox-item-img">
                            <a  href="/admin/category/{{$category->parent_category->id}}-{{$category->parent_category->slug}}">
                                <img style="width: 50px;height: 50px" class="img-thumbnail" src="{{$category->parent_category->image}}" alt="parentImage">
                                {{app()->getLocale() == 'ar' ? $category->parent_category->name_ar : $category->parent_category->name_en}}
                            </a>
                        </div>
                    </td>
                @else
                    <td>@lang('text.Root')</td>
                @endif

                <td>
                    <button type="button" wire:click="confirmRestore({{$category->id}})" class="btn btn-info waves-effect waves-light btn-sm">
                        {{__('text.Restore')}}
                    </button>
                </td>
            </tr>

        @empty
            <tr><td colspan="6" class="text-center">{{__('text.No Data Yet')}}</td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $categories->links() }}

</div>

