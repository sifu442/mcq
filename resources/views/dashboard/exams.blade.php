<!-- resources/views/exams.blade.php -->

<x-app-layout>
    <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="default-tab"
            data-tabs-toggle="#default-tab-content" role="tablist">
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg" id="ongoing-tab" data-tabs-target="#ongoing"
                    type="button" role="tab" aria-controls="ongoing" aria-selected="true">Ongoing
                    Exam/Exams</button>
            </li>
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg" id="upcoming-tab" data-tabs-target="#upcoming"
                    type="button" role="tab" aria-controls="upcoming" aria-selected="false">Upcoming Exams</button>
            </li>
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg" id="previous-tab" data-tabs-target="#previous"
                    type="button" role="tab" aria-controls="previous" aria-selected="false">Previous Exams</button>
            </li>
        </ul>
    </div>

    <div id="default-tab-content">
        <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="ongoing" role="tabpanel"
            aria-labelledby="ongoing-tab">
            @if (count($ongoingExams) > 0)
                @foreach ($ongoingExams as $exam)
                    <div class="mb-4">
                        <a href="{{ route('exam.page', ['examId' => $exam->id]) }}">
                            <ol class="max-w-lg divide-y divide-gray-200 dark:divide-gray-700 list-decimal list-inside">
                                <li class="pb-3 sm:pb-4">
                                    <span class="flex items-center space-x-4 rtl:space-x-reverse">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                                Exam: {{ $exam->name }}
                                            </p>
                                            <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                                Time: {{ $exam->duration }} Mins
                                            </p>
                                            <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                                Ends At: {{ $exam->end_date }}
                                            </p>
                                        </div>
                                    </span>
                                </li>
                            </ol>
                        </a>
                    </div>
                @endforeach
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">No ongoing exams.</p>
            @endif
        </div>

        <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="upcoming" role="tabpanel"
            aria-labelledby="upcoming-tab">
            @if (count($upcomingExams) > 0)
                @foreach ($upcomingExams as $exam)
                    <div class="mb-4">
                        <a href="{{-- route('exam.page', ['examId' => $exam->id]) --}}">
                            <ol class="max-w-lg divide-y divide-gray-200 dark:divide-gray-700 list-decimal list-inside">
                                <li class="pb-3 sm:pb-4">
                                    <span class="flex items-center space-x-4 rtl:space-x-reverse">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                                Exam: {{ $exam->name }}
                                            </p>
                                            <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                                Time: {{ $exam->duration }} Mins
                                            </p>
                                            <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                                Starts At: {{ $exam->start_date }}
                                            </p>
                                        </div>
                                    </span>
                                </li>
                            </ol>
                        </a>
                        @endforeach
                @else
                <p class="text-sm text-gray-500 dark:text-gray-400">No upcoming exams.</p>
                @endif
                    </div>

        </div>

        @if (count($previousExams) > 0)
            <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="previous" role="tabpanel"
                aria-labelledby="previous-tab">
                @foreach ($previousExams as $exam)
                    <div class="mb-4">
                        <a href="{{ route('exam.page', ['examId' => $exam->id]) }}">
                            <ol class="max-w-lg divide-y divide-gray-200 dark:divide-gray-700 list-decimal list-inside">
                                <li class="pb-3 sm:pb-4">
                                    <span class="flex items-center space-x-4 rtl:space-x-reverse">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                                Exam: {{ $exam->name }}
                                            </p>
                                            <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                                Time: {{ $exam->duration }} Mins
                                            </p>
                                            <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                                Ended At: {{ $exam->end_date }}
                                            </p>
                                        </div>
                                    </span>
                                </li>
                            </ol>
                        </a>
                    </div>
                @endforeach
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">No previous exams.</p>
        @endif
    </div>
    </div>



</x-app-layout>
