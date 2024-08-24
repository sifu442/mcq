<div class="max-w-4xl mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold text-center mb-6">Exam Results</h1>
    @foreach ($examResponses as $response)
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-center mb-4">{{ $response->user->name }}'s Response:</h2>
            <p class="text-lg text-center mb-4">Total Score: <span class="font-bold">{{ $response->total_score }}</span></p>
            @foreach ($response->response_data as $questionData)
                <div class="mb-6">
                    <h3 class="text-xl font-semibold mb-2">{{ $questionData['question'] }}</h3>
                    <ul class="list-[upper-alpha] list-inside">
                        @foreach ($questionData['options'] as $option)
                            <li class="ps-4 border border-gray-200 rounded-md dark:border-gray-700 py-3 my-2 drop-shadow-lg
                            {{ $questionData['user_input'] == $option ? ($questionData['user_input'] == $questionData['correct_answer'] ? 'bg-green-200' : 'bg-red-200') : ($questionData['correct_answer'] == $option ? 'bg-green-100' : 'bg-white') }}">
                                {{ strip_tags($option) }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
        <hr class="my-6">
    @endforeach
</div>
