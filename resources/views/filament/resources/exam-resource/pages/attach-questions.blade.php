<x-filament-panels::page>
    <div wire:ignore class="mb-6">
        <label for="editor" class="block mb-2 text-text">Title<span style="color: red">*</span></label>
        <textarea id="editor" class="w-full p-2 border border-gray-300 rounded-md"></textarea>
        @error('questionTitle') <span class="text-danger">{{ $message }}</span> @enderror
    </div>
    <div id="search-results" class="mt-4 rounded-md">
        @if ($searchResults)
            <ul class="list-none pl-5">
                @foreach ($searchResults as $result)
                    <li>
                        <button
                            class="text-primary bg-base border border-primary p-2 rounded-md hover:bg-primary hover:text-white transition-colors"
                            onclick="replaceContent(
                                    '{{ addslashes($result->title) }}',
                                    '{{ $result->id }}',
                                    '{{ $result->subject_id }}',
                                    '{{ addslashes($result->last_appeared) }}',
                                    '{{ addslashes($result->post) }}',
                                    '{{ \Carbon\Carbon::parse($result->date)->format('Y-m-d') }}',
                                    '{{ addslashes($result->option_a) }}',
                                    '{{ addslashes($result->option_b) }}',
                                    '{{ addslashes($result->option_c) }}',
                                    '{{ addslashes($result->option_d) }}',
                                    '{{ addslashes($result->explanation) }}',
                                    '{{ addslashes($result->right_answer) }}'
                                )">
                            {{ $result->title }}
                        </button>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-text">No results found.</p>
        @endif
    </div>

    <div class="rounded-md grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="question_id" class="block mb-2 text-text">Question ID</label>
            <input type="number" id="question_id" wire:model.defer="id"
                class="w-full border border-gray-300 rounded-md" />
        </div>

        <div>
            <label for="subject" class="block text-heading font-medium mb-2">Subject<span style="color: red">*</span></label>
            <select id="subject" wire:model="subject_id" class="w-full border border-gray-300 rounded-md p-2">
                <option value="" selected>Select A Subject</option>
                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                @endforeach
            </select>
            @error('subject_id') <span class="text-danger pt-2">{{ $message }}</span> @enderror
        </div>

        <!-- Text Field for Exam Name -->
        <div>
            <label for="last_appeared" class="block mb-2 text-text">Exam Name</label>
            <input type="text" id="last_appeared" wire:model.defer="last_appeared"
                class="w-full border border-gray-300 rounded-md" />
        </div>

        <!-- Text Field for Post -->
        <div>
            <label for="post" class="block mb-2 text-text">Post</label>
            <input type="text" id="post" wire:model="post"
                class="w-full p-2 border border-gray-300 rounded-md" />
        </div>

        <!-- Date Field -->
        <div>
            <label for="date" class="block mb-2 text-text">Date</label>
            <input type="date" id="date" wire:model.defer="date"
                class="w-full p-2 border border-gray-300 rounded-md" onclick="this.showPicker()" />
        </div>
    </div>


    <div class="bg-white border rounded-lg ">
        <div class="border border-b-2 py-5">
            <p class="px-3 font-bold">Options<span style="color: red">*</span></p>
        </div>
        <div wire:ignore class="px-3 py-3">
            <label for="option_A">Option A</label>
            <textarea id="option_A" wire:model.defer="option_a"></textarea>
            @error('option_a') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div wire:ignore class="px-3 py-3">
            <label for="option_B">Option B</label>
            <textarea id="option_B" wire:model.defer="option_b"></textarea>
            @error('option_b') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div wire:ignore class="px-3 py-3">
            <label for="option_C">Option C</label>
            <textarea id="option_C" wire:model.defer="option_c"></textarea>
            @error('option_c') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div wire:ignore class="px-3 py-3">
            <label for="option_D">Option D</label>
            <textarea id="option_D" wire:model.defer="option_d"></textarea>
            @error('option_d') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <div>
            <div class="p-3 py-3">
                <label for="right_answer" class="block text-heading font-medium mt-5">Right Answer<span style="color: red">*</span></label>
                <select id="right_answer" wire:model.defer="right_answer" class="w-full rounded-md">
                    <option selected>Select Right Answer</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
                @error('right_answer') <span class="text-danger mt-2">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>

    <!-- CKEditor for Explanation -->
    <div wire:ignore class="mb-6">
        <label for="explanation" class="block mb-2 text-text">Explanation</label>
        <textarea id="explanation" wire:model.defer="explanation" class="w-full p-2 border border-gray-300 rounded-xl"></textarea>
    </div>
    <button type="button" id="submit-button" wire:click="submitForm" class="w-">
        Submit
    </button>


    <!-- Include CKEditor script -->
    <script src="{{ asset('vendor/ckeditor5/build/ckeditor.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let editorInstances = {};

            // Initialize CKEditor for the main title
            ClassicEditor
                .create(document.querySelector('#editor'))
                .then(editor => {
                    editorInstances['editor'] = editor;
                    editor.model.document.on('change:data', () => {
                        const content = editor.getData();
                        Livewire.dispatch('inputSearchTerm', {
                            content
                        });
                        Livewire.dispatch('updateTitle', {
                            content
                        });
                    });
                })
                .catch(error => {
                    console.error('Error initializing CKEditor for Title:', error);
                });

            // Initialize CKEditor for each question field
            const optionFields = ['A', 'B', 'C', 'D'];
            optionFields.forEach((label, index) => {
                ClassicEditor
                    .create(document.querySelector(`#option_${label}`))
                    .then(editor => {
                        editorInstances[`option_${label}`] = editor;
                        editor.model.document.on('change:data', () => {
                            const content = editor.getData();
                            Livewire.dispatch(`updateOption${label}`, {
                                content
                            });
                        });
                    })
                    .catch(error => {
                        console.error(`Error initializing CKEditor for Option ${label}:`, error);
                    });
            });


            // Initialize CKEditor for Explanation field
            ClassicEditor
                .create(document.querySelector('#explanation'))
                .then(editor => {
                    editorInstances['explanation'] = editor;
                    editor.model.document.on('change:data', () => {
                        const content = editor.getData();
                        Livewire.dispatch('explanationUpdated', {
                            content
                        });
                    });
                })
                .catch(error => {
                    console.error('Error initializing CKEditor for Explanation:', error);
                });
            // Define the replaceContent function in the global scope
            window.replaceContent = function(content, id, subjectId, lastAppeared, post, date, optionA, optionB, optionC,
                optionD, explanation, rightAnswer) {

                Livewire.dispatch('updatePost', {post});
                Livewire.dispatch('updateId', {id});

                if (editorInstances['editor']) {
                    editorInstances['editor'].setData(content);
                }
                if (id) {
                    document.querySelector('#question_id').value = id;

                }
                if (subjectId) {
                    document.querySelector('#subject').value = subjectId;
                }
                if (lastAppeared) {
                    document.querySelector('#last_appeared').value = lastAppeared;
                }
                if (post) {
                    document.querySelector('#post').value = post;
                    Livewire.dispatch('updatePost', {post} );
                }
                if (date) {
                    document.querySelector('#date').value = date;
                }
                if (explanation && editorInstances['explanation']) {
                    editorInstances['explanation'].setData(explanation);
                }

                // Set data for options A, B, C, and D if editors are available
                if (editorInstances['option_A'] && optionA) {
                    editorInstances['option_A'].setData(optionA);
                }
                if (editorInstances['option_B'] && optionB) {
                    editorInstances['option_B'].setData(optionB);
                }
                if (editorInstances['option_C'] && optionC) {
                    editorInstances['option_C'].setData(optionC);
                }
                if (editorInstances['option_D'] && optionD) {
                    editorInstances['option_D'].setData(optionD);
                }

                if (rightAnswer) {
                    document.querySelector('#right_answer').value = rightAnswer;
                }
            };


            window.addEventListener('replaceEditorContent', event => {
                if (editorInstances['editor']) {
                    editorInstances['editor'].setData(event.detail.content);
                }
            });

            window.syncSubjectIdBeforeSubmit = function() {
                const subjectId = document.querySelector('#subject').value;
                const subjectDropdown = document.querySelector('#subject');
                subjectDropdown.dispatchEvent(new Event('change'));
                Livewire.dispatch('subject_id', subjectId);
            };

            // Attach click event listener to submit button
            document.querySelector('#submit-button').addEventListener('click', function() {
                syncSubjectIdBeforeSubmit();
                Livewire.dispatch('submitForm');
            });
        });
    </script>
</x-filament-panels::page>
