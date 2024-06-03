<div x-data="{ selectedCount: 0, answeredQuestions: [] }">
    <div class="text-center">
        <span class="text-lg font-bold">Time Left</span>
        <div x-data="countdownTimer({{ $duration * 60 }}, '@lang('messages.minutes')', '@lang('messages.seconds')', '@lang('messages.times_up')')" x-init="startTimer()">
            <span x-text="timeDisplay"></span>
            <br>
            <span class="text-sm font-bold">Selected: </span><span x-text="selectedCount"></span>
        </div>
    </div>
    <form wire:submit.prevent="submitExam">
        <ol class="list-decimal list-inside">
        @foreach ($exam->questions as $question)
            <li class="font-semibold">
                {{ $question->title }}
                <ul>
                @foreach ($question->options as $option)
                    <li>
                        <div class="flex items-center ps-4 border bg-white border-gray-200 rounded-md dark:border-gray-700 py-2 my-2 drop-shadow-lg">
                            <input type="checkbox" wire:model="answers.{{ $question->id }}"
                                   value="{{ $option['options'] }}"
                                   id="option{{ $question->id }}_{{ $loop->index }}"
                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                   x-on:click="if (!answeredQuestions.includes({{ $question->id }})) { answeredQuestions.push({{ $question->id }}); selectedCount++; }" onclick="">
                            <label for="option{{ $question->id }}_{{ $loop->index }}"
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
                    @this.call('submitExam'); // If you need to ensure the context is the current component instance
                },
            }
        }
    </script>

</div>
