@if($errors->any())
    <div class="alert alert-danger alert-dismissible mt-3">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><i class="fas fa-times"></i></button>
        <div class="font-weight-bold">{{__('text.Whoops! Something went wrong.')}}</div>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
