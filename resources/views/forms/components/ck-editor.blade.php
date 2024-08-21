<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div wire:ignore x-data="{ state: $wire.entangle('{{ $getStatePath() }}') }" x-init="ClassicEditor
        .create($refs.content)
        .then(editor => {
            editor.model.document.on('change:data', () => {
                $refs.content.value = editor.getData();
                state = editor.getData();
            });

            @this.on('fillEditor', content => {
                editor.setData(content);
            });
        })
        .catch(error => {
            console.error(error);
        });">
        <textarea wire:ignore x-ref="content" x-bind:value="state"></textarea>
    </div>

    <!-- Include the Livewire search component -->
    @livewire('c-k-editor-search')

    <script src="{{ asset('vendor/ckeditor5/build/ckeditor.js') }}"></script>
</x-dynamic-component>
