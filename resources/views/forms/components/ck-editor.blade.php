<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div wire:ignore x-data="{ state: $wire.entangle('{{ $getStatePath() }}') }" x-init="ClassicEditor
        .create($refs.content)
        .then(editor => {
            editor.model.document.on('change:data', () => {
                $refs.content.value = editor.getData();
                state = editor.getData();

                // Trigger search on data change if needed
                if ($refs.content.value.length > 2) {
                    $wire.search();
                }
            });
        })
        .catch(error => {
            console.error(error);
        });">
        <textarea wire:ignore x-ref="content" x-bind:value="state"></textarea>
    </div>

    <!-- Render Search Results -->
    @if(!empty($searchResults))
        <ul class="search-results">
            @foreach($searchResults as $result)
                <li wire:click="selectResult('{{ $result['title'] }}')">{{ $result['title'] }}</li>
            @endforeach
        </ul>
    @endif

    <script src="{{ asset('vendor/ckeditor5/build/ckeditor.js') }}"></script>
</x-dynamic-component>
