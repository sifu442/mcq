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
                            <ul class="list-[upper-alpha] list-inside">
                                @foreach ($questionData['options'] as $option)
                                    <li class="align-middle items-center ps-4 border border-gray-200 rounded-md dark:border-gray-700 py-5 my-5 drop-shadow-lg">
                                            {{ strip_tags($option) }}
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    </ul>
                </div>
            @endforeach
        </div>
        <hr>
    @endforeach
</div>
