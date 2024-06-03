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
                            <ul class="list-[upper-alpha]">
                                @foreach ($questionData['options'] as $option)
                                    <li>
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
