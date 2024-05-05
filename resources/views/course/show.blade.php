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
                Number of exams:
            </div>

            <div class="py-3 text-gray-800 text-lg text-justify">
                Start of Exam:
            </div>

            {{-- <div class="flex items-center space-x-4 mt-10">
            <a href="#" class="bg-blue-400 text-white rounded-xl px-3 py-1 text-base">
                Tailwind</a>
            <a href="#" class="bg-red-400 text-white rounded-xl px-3 py-1 text-base">
                Laravel</a>
        </div> --}}

           
</x-app-layout>
