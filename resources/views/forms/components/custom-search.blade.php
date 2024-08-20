<div x-data="{
    state: @entangle('state'),
    searchResults: @entangle('searchResults'),
    initEditor() {
        ClassicEditor
            .create($refs.content)
            .then(editor => {
                this.editor = editor;
                editor.model.document.on('change:data', () => {
                    this.state = editor.getData();
                });

                window.addEventListener('set-editor-content', event => {
                    this.editor.setData(event.detail.content);
                });
            })
            .catch(error => {
                console.error(error);
            });
    },
}"
x-init="initEditor()"
>
<textarea x-ref="content" x-bind:value="state"></textarea>
<div class="search-results mt-2">
    <ul>
        <template x-for="result in searchResults" :key="result.id">
            <li @click="$wire.setState(result.content)" x-text="result.title" class="cursor-pointer hover:bg-gray-200"></li>
        </template>
    </ul>
</div>
<script src="{{ asset('vendor/ckeditor5/build/ckeditor.js') }}"></script>
</div>
