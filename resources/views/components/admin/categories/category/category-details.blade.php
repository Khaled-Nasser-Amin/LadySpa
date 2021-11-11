<div class="col-lg-2">
    <div class="mt-4">
        <img class="img-thumbnail w-100" style="width: 200px; height: 300px" src="{{$category->image}}" alt="slide-image" />
    </div>
    <!-- end slider -->

    <div class="mt-4 justify-content-center">
        <h6>{{__('text.Category Name')}} : <span class="text-pink">{{app()->getLocale()=='ar' ? $category->name_ar : $category->name_en}}</span></h6>
        <h6>{{__('text.Number of Products')}} : <span class="text-pink">{{$category->products->count()}}</span></h6>

    </div>

</div>
