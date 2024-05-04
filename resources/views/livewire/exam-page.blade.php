
<div x-data="{ selectedCount: 0, currentQuestionId: null }">
    <div class="text-center">
        <span class="text-lg font-bold">Time Left</span>
        <div x-data="countdownTimer({{ $duration * 60 }}, '@lang('messages.minutes')', '@lang('messages.seconds')', '@lang('messages.times_up')')" x-init="startTimer()">
            <span x-text="timeDisplay"></span>
            <br>
            <span class="text-sm font-bold">Selected: </span><span x-text="selectedCount"></span>
        </div>
    </div>
    <form wire:submit.prevent="submitExam">
        <ol class="list-decimal list-inside"> <!-- Start of questions ordered list -->
        @foreach ($exam->questions as $question)
            <li class="font-semibold"> <!-- Each question is a list item -->
                {{ $question->title }}
                <ul> <!-- Options for the question -->
                @foreach ($question->options as $option)
                    <li>
                        <div class="flex items-center ps-4 border bg-white border-gray-200 rounded-md dark:border-gray-700 py-2 my-2 drop-shadow-lg">
                            <input type="radio" wire:model="answers.{{ $question->id }}"
                                   value="{{ $option['options'] }}"
                                   id="option{{ $loop->parent->index }}_{{ $loop->index }}"
                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                   x-on:click="selectedCount += (questionId !== currentQuestionId) ? 1 : 0; currentQuestionId = questionId">
                            <label for="option{{ $loop->parent->index }}_{{ $loop->index }}"
                                   class="w-full py-4 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                {{ strip_tags($option['options']) }}
                            </label>
                        </div>
                    </li>
                @endforeach
                </ul>
            </li>
        @endforeach
        </ol> <!-- End of questions ordered list -->
        <div class="flex justify-center mt-4">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-10 py-5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Submit</button>
        </div>
    </form>
</div>


