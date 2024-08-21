@php
    $options = $field->getOptions(); // Get options dynamically
    $selectedValue = $getState(); // Get the current selected value
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <textarea id="ckeditor-editor" name="ckeditor-editor"></textarea>
    <select
        id="{{ $getId() }}"
        name="{{ $getName() }}"
        {{ $attributes->merge($getExtraAttributes()) }}
        {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}"
    >
        @foreach($options as $value => $label)
            <option value="{{ $value }}" {{ $value == $selectedValue ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>

    <script src="{{ asset('vendor/ckeditor5/build/ckeditor.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            ClassicEditor
                .create(document.querySelector('#ckeditor-editor'))
                .then(editor => {
                    editor.model.document.on('change:data', () => {
                        let searchText = editor.getData(); // Get the CKEditor content

                        // Call a function to filter select options
                        filterOptions(searchText);
                    });
                })
                .catch(error => {
                    console.error(error);
                });
        });

        function filterOptions(searchText) {
            let selectElement = document.querySelector('#{{ $getId() }}');
            let options = selectElement.querySelectorAll('option');

            options.forEach(option => {
                let label = option.textContent.toLowerCase();
                let shouldShow = label.includes(searchText.toLowerCase());
                option.style.display = shouldShow ? 'block' : 'none';
            });
        }
    </script>
</x-dynamic-component>
