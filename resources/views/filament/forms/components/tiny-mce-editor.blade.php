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
