<x-app-layout>
    <div class="">
        <h2 class="text-center">Welcome, {{ $user->name }}</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- First component-->
        @if ($enrollments->isEmpty())
            <h2>You are not enrolled in any courses.</h2>
        @else
            @foreach ($enrollments as $enrollment)
                <a href="{{ route('exams', ['course' => $course->id]) }}" class="no-underline">
                    <div class="max-w-sm bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-900 transition duration-150 ease-in-out">
                        {{--<img class="rounded-t-lg w-full" src="/path/to/your/image.jpg" alt="{{ $enrollment->course->title }}" />--}}
                        <div class="p-5">
                            <h2 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                                {{ $enrollment->course->title }}
                            </h2>
                            <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">{{ $enrollment->course->time_span }} day/days</p>
                            <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">{{ $enrollment->course->price }} Taka</p>
                            <div class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                Read more
                                <svg class="rtl:rotate-180 w-3.5 h-3.5 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        @endif
    </div>
</x-app-layout>
