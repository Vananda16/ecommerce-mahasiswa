@props(['name', 'label' => null, 'value' => null, 'required' => false, 'rows' => 3, 'help' => null])

<div class="mb-3">
    @if($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }}@if($required) <span class="text-danger">*</span>@endif
        </label>
    @endif
    <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}"
              @required($required)
              {{ $attributes->class(['form-control', 'is-invalid' => $errors->has($name)]) }}>{{ old($name, $value) }}</textarea>
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    @if($help)
        <div class="form-text">{{ $help }}</div>
    @endif
</div>
