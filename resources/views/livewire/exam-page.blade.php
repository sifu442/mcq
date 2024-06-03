<div x-data="examComponent()">
    <div class="text-center">
        <span class="text-lg font-bold">Time Left</span>
        <div x-data="countdownTimer({{ $duration * 60 }}, '@lang('messages.minutes')', '@lang('messages.seconds')', '@lang('messages.times_up')')" x-init="startTimer()">
            <span x-text="timeDisplay"></span>
            <br>
            <span class="text-sm font-bold">Answered: </span><span x-text="selectedCount"></span>
            <span class="text-sm font-bold">Unanswered: </span><span x-text="unansweredCount"></span>
        </div>
    </div>
    <form wire:submit.prevent="submitExam">
        <ol class="list-decimal list-inside">
            @foreach ($exam->questions as $question)
                <li class="font-semibold">
                    <?php echo $question->title ?>
                    <ul>
                        @foreach ($question->options as $index => $option)
                            <li>
                                <div class="flex items-center ps-4 border bg-white border-gray-200 rounded-md dark:border-gray-700 py-2 my-2 drop-shadow-lg">
                                    <input type="checkbox"
                                           value="{{ $option['options'] }}"
                                           id="option{{ $question->id }}_{{ $index }}"
                                           class="hidden peer"
                                           x-on:change="toggleSelection($event, {{ $question->id }}, '{{ $option['options'] }}')"
                                           x-bind:checked="answers[{{ $question->id }}] === '{{ $option['options'] }}'">
                                    <label for="option{{ $question->id }}_{{ $index }}"
                                           class="flex items-center justify-center w-8 h-8 rounded-full border border-gray-300 text-gray-600 peer-checked:bg-blue-600 peer-checked:text-white cursor-pointer">
                                        {{ chr(65 + $index) }}
                                    </label>
                                    <label for="option{{ $question->id }}_{{ $index }}"
                                           class="w-full py-4 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                        {{ strip_tags($option['options']) }}
                                    </label>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </ol>
        <div class="flex justify-center mt-4">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-10 py-5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Submit</button>
        </div>
    </form>
    <script>
        function examComponent() {
            return {
                selectedCount: 0,
                unansweredCount: @entangle('unansweredCount'),
                answers: @entangle('answers'),
                init() {
                    this.unansweredCount = {{ $exam->questions->count() }} - Object.keys(this.answers).length;
                },
                toggleSelection(event, questionId, option) {
                    let checkbox = event.target;

                    // Deselect other options for the same question
                    document.querySelectorAll(`input[type="checkbox"][id^="option${questionId}_"]`).forEach(el => {
                        if (el !== checkbox) {
                            el.checked = false;
                        }
                    });

                    // Update the answer model for Livewire
                    if (checkbox.checked) {
                        if (!this.answers[questionId]) {
                            this.selectedCount++;
                            this.unansweredCount--;
                        }
                        this.answers[questionId] = option;
                    } else {
                        if (this.answers[questionId]) {
                            this.selectedCount--;
                            this.unansweredCount++;
                        }
                        this.answers[questionId] = null;
                    }
                }
            }
        }

        function countdownTimer(durationInSeconds, minutesLabel, secondsLabel, timesUpMessage) {
            return {
                remainingSeconds: durationInSeconds,
                timeDisplay: '',

                startTimer() {
                    this.timer();
                    this.interval = setInterval(() => this.timer(), 1000);
                },

                timer() {
                    let minutes = Math.floor(this.remainingSeconds / 60);
                    let seconds = this.remainingSeconds % 60;

                    if (this.remainingSeconds <= 0) {
                        clearInterval(this.interval);
                        this.timeDisplay = timesUpMessage;
                        // Automatically submit the exam if the countdown reaches zero
                        this.autoSubmitExam();
                    } else {
                        this.timeDisplay =
                            `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                        this.remainingSeconds--;
                    }
                },

                autoSubmitExam() {
                    @this.call('submitExam'); // Ensure the context is the current component instance
                },
            }
        }
    </script>
</div>
