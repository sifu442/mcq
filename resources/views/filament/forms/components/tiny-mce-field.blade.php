<x-forms::field-wrapper
    :id="$getId()"
    :label="$getLabel()"
    :helper="$getHelper()"
    :hint="$getHint()"
    :hintsPosition="$getHintsPosition()"
    :required="$isRequired()"
    :statePath="$getStatePath()"
>
    <div
        x-data="{
            editor: null,
            init() {
                this.editor = tinymce.init({
                    target: this.$refs.editor,
                    {{ $applyStateBindingModifiers('init') }}={{ $getStatePath() }},
                    setup: (editor) => {
                        // Additional TinyMCE configuration options
                    },
                    // Additional TinyMCE configuration options
                })
            }
        }"
        x-init="init()"
    >
        <textarea x-ref="editor" {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}"></textarea>
    </div>
</x-forms::field-wrapper>
