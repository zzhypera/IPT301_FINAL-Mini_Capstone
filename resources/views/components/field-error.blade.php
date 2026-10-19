@props(['field'])

@error($field)
    <div class="invalid-feedback d-block" id="{{ $field }}-error">{{ $message }}</div>
@enderror

