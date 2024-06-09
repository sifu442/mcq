<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <textarea id="tiny-mce-editor-{{ $getId() }}" {{ $applyStateBindingModifiers('wire:model.defer') }}="{{ $getStatePath() }}"></textarea>

    @once
        @push('scripts')
            <script src="https://cdn.tiny.cloud/1/your-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
        @endpush
    @endonce

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                tinymce.init({
                    selector: '#tiny-mce-editor-{{ $getId() }}',
                    theme: "silver",
        plugins: [ "image code table link media codesample"],
        toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | outdent indent | media | table',
        //paste Core plugin options
        paste_block_drop: false,
        paste_data_images: true,
        paste_as_text: true,
                    setup: function (editor) {
                        editor.on('Change', function () {
                            @this.set('{{ $getStatePath() }}', editor.getContent());
                        });
                    },
                    readonly: false, // Ensure the editor is writable
                });
            });
        </script>
    @endpush
</x-dynamic-component>
