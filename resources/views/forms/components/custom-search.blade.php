@php
    $options = $getOptions();
@endphp
<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <select
        id="{{ $getId() }}"
        name="{{ $getName() }}"
        {{ $attributes->merge($getExtraAttributes()) }}
    >
        @foreach($options as $value => $label)
            <option value="{{ $value }}" {{ $isSelected($value) ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>

    <script src="{{ asset('vendor/ckeditor5/build/ckeditor.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            ClassicEditor
                .create(document.querySelector('#{{ $getId() }}'))
                .catch(error => {
                    console.error(error);
                });
        });
    </script>
</x-dynamic-component>
