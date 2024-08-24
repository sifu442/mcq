<div x-data="examComponent()" class="relative min-h-screen">
    <!-- Main Content -->
    <div class="lg:flex lg:relative">
        <div class="flex-grow">
            <form wire:submit.prevent="submitExam">
                <ol class="list-decimal list-inside">
                    @foreach ($exam->questions as $question)
                        <li class="p-2.5 md:p-5">
                            <span x-html="sanitizeHtml(`{!! $question->title !!}`)"></span>
                            <ul>
                                @foreach ($question->options as $index => $option)
                                    <li>
                                        <div class="flex items-center ps-4 border bg-white border-gray-200 rounded-md dark:border-gray-700 py-2 my-2 drop-shadow-lg">
                                            <input type="radio"
                                                   value="{{ $option['options'] }}"
                                                   id="option{{ $question->id }}_{{ $index }}"
                                                   name="question{{ $question->id }}"
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
        </div>

        <!-- Right Sidebar (Visible on Desktop Only) -->
        <div class="hidden lg:block lg:w-64 bg-blue-50 dark:bg-gray-800 p-4 sticky top-0 h-screen overflow-y-auto">
            <!-- Timer -->
            <div class="text-center mb-4">
                <span class="text-lg font-bold">Time Left</span>
                <div x-data="countdownTimer({{ $duration * 60 }}, '@lang('messages.minutes')', '@lang('messages.seconds')', '@lang('messages.times_up')')" x-init="startTimer()">
                    <span x-text="timeDisplay" class="block text-2xl font-bold"></span>
                </div>
            </div>

            <!-- Exam Details -->
            <div class="text-lg font-bold text-blue-700 dark:text-gray-200 mb-4">Exam Details</div>
            <ul class="text-sm text-gray-700 dark:text-gray-300">
                <li><strong>Total Questions:</strong> {{ $exam->questions->count() }}</li>
                <li><strong>Answered:</strong> <span x-text="answeredCount"></span></li>
                <li><strong>Unanswered:</strong> <span x-text="unansweredCount"></span></li>
            </ul>

            <!-- Exam Name -->
            <div class="text-center mt-8 text-lg font-bold text-blue-700 dark:text-gray-200">
                {{ $exam->name }}
            </div>
        </div>
    </div>

    <!-- Floating Top for Mobile/Tablets -->
    <div class="lg:hidden fixed top-0 left-0 right-0 bg-blue-50 dark:bg-gray-800 p-4 shadow-lg flex items-center justify-between z-50" style="margin-top: 4rem;">
        <!-- Timer -->
        <div class="text-center">
            <span class="text-lg font-bold">Time Left</span>
            <div x-data="countdownTimer({{ $duration * 60 }}, '@lang('messages.minutes')', '@lang('messages.seconds')', '@lang('messages.times_up')')" x-init="startTimer()">
                <span x-text="timeDisplay" class="block text-xl font-bold"></span>
            </div>
        </div>

        <!-- Exam Details -->
        <div class="text-sm text-gray-700 dark:text-gray-300 text-center">
            <div class="font-bold">Answered</div>
            <div x-text="answeredCount"></div>
            <div class="font-bold">Unanswered</div>
            <div x-text="unansweredCount"></div>
        </div>
    </div>
</div>

<script>
    function examComponent() {
        return {
            answeredCount: 0,
            unansweredCount: {{ $exam->questions->count() }},
            answers: @entangle('answers'),
            init() {
                this.updateCounts();
            },
            toggleSelection(event, questionId, option) {
                let selectedOption = event.target.value;
                this.answers[questionId] = selectedOption;
                this.updateCounts();
            },
            updateCounts() {
                this.answeredCount = Object.values(this.answers).filter(value => value !== null).length;
                this.unansweredCount = {{ $exam->questions->count() }} - this.answeredCount;
            },
            sanitizeHtml(input) {
                const allowedTags = ['b', 'i', 'strong', 'em'];
                const doc = new DOMParser().parseFromString(input, 'text/html');
                const elements = doc.body.getElementsByTagName('*');

                for (let i = elements.length - 1; i >= 0; i--) {
                    const element = elements[i];
                    if (!allowedTags.includes(element.nodeName.toLowerCase())) {
                        element.outerHTML = element.innerHTML;
                    }
                }

                return doc.body.innerHTML;
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
