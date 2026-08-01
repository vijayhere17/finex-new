<div class="form-group mb-3">
    <select class="form-control" name="{{ $name }}" id="{{ $id }}" style="height: 58px;">
        @foreach($options as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
        @endforeach
    </select>
</div>    