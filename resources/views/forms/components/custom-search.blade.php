<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div wire:ignore x-data="{
        state: $wire.entangle('{{ $getStatePath() }}'),
        searchResults: [],
        search() {
            @this.call('getSearchResults').then(results => {
                this.searchResults = results;
            });
        }
    }" x-init="ClassicEditor
        .create($refs.content)
        .then(editor => {
            editor.model.document.on('change:data', () => {
                $refs.content.value = editor.getData();
                state = editor.getData();
                this.search();
            });
        })
        .catch(error => {
            console.error(error);
        });">
        <textarea wire:ignore x-ref="content" x-bind:value="state"></textarea>
        <div class="search-results mt-2">
            <ul>
                <template x-for="result in searchResults" :key="result.id">
                    <li @click="state = result.content" x-text="result.title" class="cursor-pointer hover:bg-gray-200"></li>
                </template>
            </ul>
        </div>
    </div>
    <script src="{{ asset('vendor/ckeditor5/build/ckeditor.js') }}"></script>
</x-dynamic-component>
