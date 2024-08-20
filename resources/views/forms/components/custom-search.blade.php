<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div wire:ignore x-data="{
        state: $wire.entangle('{{ $getStatePath() }}'),
        searchResults: @json($searchResults ?? []),
        editor: null,
        initEditor() {
            ClassicEditor
                .create($refs.content)
                .then(editor => {
                    this.editor = editor;
                    editor.model.document.on('change:data', () => {
                        $refs.content.value = editor.getData();
                        this.state = editor.getData();
                        this.search();
                    });
                })
                .catch(error => {
                    console.error(error);
                });
        },
        search() {
            @this.call('search').then(results => {
                this.searchResults = results;
            });
        }
    }" x-init="initEditor()">
        <textarea x-ref="content" x-bind:value="state"></textarea>
        <div class="search-results mt-2">
            <ul>
                <template x-for="result in searchResults" :key="result.id">
                    <li @click="state = result.content; editor.setData(result.content)" x-text="result.title" class="cursor-pointer hover:bg-gray-200"></li>
                </template>
            </ul>
        </div>
    </div>
    <script src="{{ asset('vendor/ckeditor5/build/ckeditor.js') }}"></script>
</x-dynamic-component>
