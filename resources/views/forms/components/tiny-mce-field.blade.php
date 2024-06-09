<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
    :id="$getId()"
    :label="$getLabel()"
    :hint="$getHint()"
    :required="$isRequired()"
    :statePath="$getStatePath()"
    >
    <div
    x-data="{
        editor: null,
        isTinyMCELoaded: false,
        initEditor() {
            if (this.isTinyMCELoaded) {
                this.editor = tinymce.init({
                    target: this.$refs.editor,
                    {{ $applyStateBindingModifiers('init') }}={{ $getStatePath() }},
                    menubar: true,
                    plugins: [
                        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap',
                        'anchor', 'searchreplace', 'visualblocks', 'code',
                        'fullscreen', 'insertdatetime', 'media', 'table', 'help', 'wordcount'
                    ],
                    toolbar: 'undo redo | blocks | ' +
                        'bold italic forecolor | alignleft aligncenter ' +
                        'alignright alignjustify | bullist numlist outdent indent | ' +
                        'removeformat | help',
                    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
                })
            }
        }
    }"
    x-init="
        tinymce.PluginManager.load('tinymce/tinymce.min.js', function() {
            isTinyMCELoaded = true;
            initEditor();
        });
    "
>
    <textarea x-ref="editor" {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}"></textarea>
</div>
</x-dynamic-component>
