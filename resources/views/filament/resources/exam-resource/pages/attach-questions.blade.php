<x-filament::page>
    <form wire:submit.prevent="submit">
        <div class="space-y-4">
            <!-- Title Field -->
            <div wire:ignore>
                <label for="questionTitle">Title</label>
                <textarea wire:model.defer="questionTitle" id="questionTitle" class="ckeditor"></textarea>
            </div>

            <!-- Post Field -->
            <div>
                <label for="post">Post</label>
                <input wire:model.defer="post" type="text" id="post" class="form-input">
            </div>

            <!-- Previous Exam Field -->
            <div>
                <label for="previous_exam">Previous Exam</label>
                <input wire:model.defer="previous_exam" type="text" id="previous_exam" class="form-input">
            </div>

            <!-- Subject Select Field -->
            <div>
                <label for="subject">Subject</label>
                <select wire:model.defer="subject_id" id="subject" class="form-select">
                    <option value="">Select Subject</option>
                    @foreach($subjects as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Date Field -->
            <div>
                <label for="date">Date</label>
                <input wire:model.defer="date" type="date" id="date" class="form-input">
            </div>

            <!-- Option Fields with Checkbox for Correct Answer -->
            @foreach($options as $index => $option)
                <div class="space-y-2">
                    <!-- Convert index to alphabetical label -->
                    @php
                        $alphabet = chr(65 + $index); // 65 is ASCII value for 'A'
                    @endphp

                    <label for="option_{{ $index }}">Option {{ $alphabet }}</label>
                    <div wire:ignore>
                        <textarea wire:model.defer="options.{{ $index }}.options" id="option_{{ $index }}" class="ckeditor"></textarea>
                    </div>

                    <!-- Checkbox for correct answer -->
                    <div>
                        <label>
                            <input type="radio" wire:click="setCorrectAnswer({{ $index }})" name="correct_answer" value="{{ $index }}"
                            @if($options[$index]['is_correct']) checked @endif>
                            Correct Answer
                        </label>
                    </div>
                </div>
            @endforeach

            <!-- Explanation Field -->
            <div wire:ignore>
                <label for="explanation">Explanation</label>
                <textarea wire:model.defer="explanation" id="explanation" class="ckeditor"></textarea>
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>

    <!-- CKEditor Script -->
    <script src="{{ asset('vendor/ckeditor5/build/ckeditor.js') }}"></script>
    <script>
        document.addEventListener('livewire:init', function () {
            // Initialize CKEditor for each textarea with class 'ckeditor'
            document.querySelectorAll('.ckeditor').forEach((el) => {
                ClassicEditor
                    .create(el)
                    .then(editor => {
                        // When CKEditor content changes, dispatch an input event to sync with Livewire
                        editor.model.document.on('change:data', () => {
                            el.value = editor.getData();
                            el.dispatchEvent(new Event('input'));
                        });
                    })
                    .catch(error => {
                        console.error('CKEditor error:', error);
                    });
            });
        });

        // Re-initialize CKEditor after Livewire updates
        document.addEventListener("livewire:load", () => {
            Livewire.hook('message.processed', (message, component) => {
                document.querySelectorAll('.ckeditor').forEach((el) => {
                    if (!el.classList.contains('ck-editor__editable')) {
                        ClassicEditor
                            .create(el)
                            .then(editor => {
                                editor.model.document.on('change:data', () => {
                                    el.value = editor.getData();
                                    el.dispatchEvent(new Event('input'));
                                });
                            })
                            .catch(error => {
                                console.error('CKEditor error:', error);
                            });
                    }
                });
            });
        });
    </script>
</x-filament::page>
