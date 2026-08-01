<div class="form-check">
    <input style="position: unset;" class="form-check-input input-primary" type="{{ $type }}" name="{{ $name }}" id="{{ $id }}" {{ $ischeck == 1 ? 'checked=""' : '' }} >
    <label class="form-check-label text-muted" for="{{ $id }}">{{ $label }}</label>
</div>