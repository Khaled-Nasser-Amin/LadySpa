@if(session()->has('success'))
    <div class="alert alert-success alert-dismissible text-center mt-3">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><i class="fas fa-times"></i></button>
        {{session()->get('success')}}
    </div>
@endif
@if(session()->has('danger'))
    <div class="alert alert-danger alert-dismissible text-center mt-3">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><i class="fas fa-times"></i></button>
        {{session()->get('danger')}}
    </div>
@endif
