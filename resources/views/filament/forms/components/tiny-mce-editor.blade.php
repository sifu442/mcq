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
                    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed linkchecker a11ychecker tinymcespellchecker permanentpen powerpaste advtable advcode editimage advtemplate ai mentions tinycomments tableofcontents footnotes mergetags autocorrect typography inlinecss markdown',
toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
tinycomments_mode: 'embedded',
tinycomments_author: 'Author name',
mergetags_list: [
  { value: 'First.Name', title: 'First Name' },
  { value: 'Email', title: 'Email' },
],
ai_request: (request, respondWith) => respondWith.string(() => Promise.reject("See docs to implement AI Assistant")),
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
