<div x-data="examComponent()" x-init="init()">
    <div class="text-center">
        <span class="text-lg font-bold">Time Left</span>
        <div x-data="countdownTimer(1800, 'Time\'s up!')" x-init="startTimer()">
            <span x-text="timeDisplay"></span>
            <br>
            <span class="text-sm font-bold">Selected: </span><span x-text="selectedCount"></span>
            <br>
            <span class="text-sm font-bold">Unanswered: </span><span x-text="unansweredCount"></span>
        </div>
    </div>
    <form wire:submit.prevent="submitExam">
        <ol class="list-decimal list-inside">
            @foreach ($exam->questions as $question)
                <li class="p-2.5 md:p-5">
                    <span x-html="sanitizeHtml('{!! $question->title !!}')"></span>
                    <ul>
                        @foreach ($question->options as $index => $option)
                            <li class="relative flex items-center">
                                <div class="flex items-center ps-4 border bg-white border-gray-200 rounded-md dark:border-gray-700 py-2 my-2 drop-shadow-lg w-full">
                                    <input type="radio" value="{{ $option['options'] }}"
                                        name="question{{ $question->id }}"
                                        id="option{{ $question->id }}_{{ $index }}" class="hidden peer"
                                        x-on:change="debouncedToggleSelection($event, {{ $question->id }}, '{{ $option['options'] }}')"
                                        x-bind:checked="answers[{{ $question->id }}] === '{{ $option['options'] }}'"
                                        wire:model.lazy="answers.{{ $question->id }}">
                                    <label for="option{{ $question->id }}_{{ $index }}"
                                        class="flex items-center justify-center w-8 h-8 rounded-full border border-gray-300 text-gray-600 peer-checked:bg-blue-600 peer-checked:text-white cursor-pointer">
                                        {{ chr(65 + $index) }}
                                    </label>
                                    <label for="option{{ $question->id }}_{{ $index }}"
                                        class="w-full py-4 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                        {{ strip_tags($option['options']) }}
                                    </label>
                                    <button type="button"
                                        x-show="answers[{{ $question->id }}] === '{{ $option['options'] }}'"
                                        x-on:click="removeSelection({{ $question->id }})"
                                        class="absolute top-1/2 right-2 transform -translate-y-1/2 text-red-500 hover:text-red-700 flex items-center justify-center w-8 h-8 rounded-full border border-gray-300">
                                        <span class="text-xl">&times;</span>
                                    </button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </ol>
        <div class="flex justify-center mt-4">
            <button type="submit"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-10 py-5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Submit</button>
        </div>
    </form>

    <script>
        function examComponent() {
            return {
                selectedCount: 0,
                unansweredCount: @entangle('unansweredCount'),
                answers: @entangle('answers').defer || {},
                debounceTimeout: null,

                init() {
                    this.updateCounts();
                },

                debouncedToggleSelection(event, questionId, option) {
                    clearTimeout(this.debounceTimeout);
                    this.debounceTimeout = setTimeout(() => {
                        this.toggleSelection(event, questionId, option);
                    }, 300); // 300ms debounce delay
                },

                toggleSelection(event, questionId, option) {
                    let radio = event.target;
                    let previouslySelected = this.answers[questionId];

                    if (radio.checked) {
                        if (previouslySelected === undefined) {
                            this.selectedCount++;
                            this.unansweredCount--;
                        }
                        this.answers[questionId] = option;
                    } else {
                        if (previouslySelected === option) {
                            this.selectedCount--;
                            this.unansweredCount++;
                        }
                        delete this.answers[questionId];
                    }

                    this.updateLivewire();
                },

                removeSelection(questionId) {
                    if (this.answers && this.answers[questionId] !== undefined) {
                        this.selectedCount--;
                        this.unansweredCount++;
                        delete this.answers[questionId];
                        this.updateLivewire();
                    }
                },

                updateLivewire() {
                    @this.set('answers', this.answers);
                    @this.set('unansweredCount', this.unansweredCount);
                },

                updateCounts() {
                    this.selectedCount = Object.keys(this.answers || {}).length;
                    this.unansweredCount = {{ $exam->questions->count() }} - this.selectedCount;
                },

                sanitizeHtml(input) {
                    const allowedTags = ['b', 'i', 'strong', 'em'];
                    const doc = new DOMParser().parseFromString(input, 'text/html');
                    const elements = doc.body.getElementsByTagName('*');

                    for (let i = elements.length - 1; i >= 0; i--) {
                        const el = elements[i];
                        if (!allowedTags.includes(el.tagName.toLowerCase())) {
                            el.replaceWith(...el.childNodes);
                        }
                    }

                    return doc.body.innerHTML;
                }
            }
        }

        function countdownTimer(durationInSeconds, expiredText) {
            return {
                timeRemaining: durationInSeconds,
                timeDisplay: '',
                timerInterval: null,

                startTimer() {
                    this.updateTimeDisplay();

                    this.timerInterval = setInterval(() => {
                        this.timeRemaining--;

                        if (this.timeRemaining <= 0) {
                            clearInterval(this.timerInterval);
                            this.timeDisplay = expiredText;
                            this.submitExamForm();
                        } else {
                            this.updateTimeDisplay();
                        }
                    }, 1000);
                },

                updateTimeDisplay() {
                    const minutes = String(Math.floor(this.timeRemaining / 60)).padStart(2, '0');
                    const seconds = String(this.timeRemaining % 60).padStart(2, '0');
                    this.timeDisplay = `${minutes}:${seconds}`;
                },

                submitExamForm() {
                    // Automatically submit the exam form when time is up
                    document.querySelector('form').submit();
                }
            }
        }
    </script>
</div>
