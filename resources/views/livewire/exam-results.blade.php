<div>
    <h1 class="text-center">Exam Results</h1>
    @foreach ($examResponses as $response)
        <div>
            <h2 class="text-center">{{ $response->user->name }}'s Response:</h2>
            <p>Total Score: {{ $response->total_score }}</p>
            @foreach ($response->response_data as $questionData)
                <div>
                    <ol class="list-decimal list-inside">
                        <li>{{ $questionData['question'] }}
                            <ol type="A" class="list-inside ml-4">
                                @foreach ($questionData['options'] as $option)
                                    <li class="">
                                        <div
                                            class="flex items-center ps-4 border border-gray-200 rounded-md dark:border-gray-700 py-5 my-5 drop-shadow-lg {{ $questionData['user_input'] == $option ? ($questionData['user_input'] == $questionData['correct_answer'] ? 'bg-green-200' : 'bg-red-200') : ($questionData['correct_answer'] == $option ? 'bg-green-200' : 'bg-white') }}">
                                            {{ strip_tags($option) }}
                                        </div>
                                    </li>
                                @endforeach
                            </ol>
                        </li>
                    </ol>
                </div>
            @endforeach
        </div>
        <hr>
    @endforeach
</div>
