<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <textarea id="tiny-mce-editor-{{ $getId() }}" {{ $applyStateBindingModifiers('wire:model.defer') }}="{{ $getStatePath() }}"></textarea>

    @once
        @push('scripts')
            <script src="https://cdn.tiny.cloud/1/lrii0uxtuwcwtm989pioll20tuxfxuq3dyuyf0tzbqs7urbz/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
        @endpush
    @endonce

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                tinymce.init({
                    selector: '#tiny-mce-editor-{{ $getId() }}',
                    plugins: 'advlist autolink lists link image charmap print preview anchor searchreplace visualblocks code fullscreen insertdatetime media table paste code help wordcount',
                    toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
                    setup: function (editor) {
                        editor.on('Change', function () {
                            @this.set('{{ $getStatePath() }}', editor.getContent());
                        });
                    },
                    readonly: false,
                });
            });
        </script>
    @endpush
</x-dynamic-component>
