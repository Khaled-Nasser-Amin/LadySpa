@props(['for'])

@error($for)
    <span class="is-invalid text-danger" role="alert">
        <strong>{{ $message }}</strong>
    </span>
@enderror
