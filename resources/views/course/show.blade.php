<x-app-layout>
    <div class="">
        <img class="" src="" alt="">
        <h1 class="text-3xl">
            {{ $course->title }}
        </h1>
        <div class="mt-2 flex justify-between items-center">
            <div class="flex items-center">
                <span> Published :</span><span class="text-gray-500 mr-2">2 days ago</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.3"
                    stroke="currentColor" class="w-5 h-5 text-gray-500">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <div class="py-3 text-gray-800 text-lg text-justify">
            Subjects: {{ $subjects->pluck('name')->join(', ') }}
        </div>


        <div class="py-3 text-gray-800 text-lg text-justify">
            Gap per exam:
        </div>

        <div class="py-3 text-gray-800 text-lg text-justify">
            Number of exams: {{$course->total_exams}}
        </div>

        <div class="py-3 text-gray-800 text-lg text-justify">
            Fee: {{$course->price}} Taka
        </div>

        <div class="py-3 text-gray-800 text-lg text-justify">
            Start of Exam: {{ $futureDate }}
        </div>

        {{-- <div class="flex items-center space-x-4 mt-10">
            <a href="#" class="bg-blue-400 text-white rounded-xl px-3 py-1 text-base">
                Tailwind</a>
            <a href="#" class="bg-red-400 text-white rounded-xl px-3 py-1 text-base">
                Laravel</a>
        </div> --}}


        <div id="accordion-collapse" data-accordion="collapse">
            @foreach ($exams as $exam)
            <h2 id="accordion-collapse-heading-1">
                <button type="button"
                    class="flex items-center justify-between w-full p-5 font-medium rtl:text-right text-gray-500 border border-b-0 border-gray-200 rounded-t-xl focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-800 dark:border-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 gap-3"
                    data-accordion-target="#accordion-collapse-body-1" aria-expanded="true"
                    aria-controls="accordion-collapse-body-1">
                    <span>{{ $exam->name }}</span>
                    <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5 5 1 1 5" />
                    </svg>
                </button>
            </h2>
            <div id="accordion-collapse-body-1" class="hidden" aria-labelledby="accordion-collapse-heading-1">
                <div class="p-5 border border-bt-0 border-gray-200 dark:border-gray-700 dark:bg-gray-900">
                    <p class="mb-2 text-gray-500 dark:text-gray-400">{{ $exam->syllabus }}</p>
                </div>
            </div>
        </div>
        @endforeach
</x-app-layout>
