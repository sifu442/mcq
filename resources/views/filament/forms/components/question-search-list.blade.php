@php
    $questions = $getQuestions();
@endphp

@if (count($questions) > 0)
    <div class="flex flex-col divide-y">
        @foreach ($questions as $question)
            <div wire:key="{{ $question['id'] }}" class="flex flex-row justify-between p-3">
                <span>{{ $question['title'] }}</span>
                <button type="button" wire:click="$set('{{ $getStatePath() }}', '{{ $question['title'] }}')" class="text-blue-500 underline">
                    Select
                </button>
            </div>
        @endforeach
    </div>
@endif
