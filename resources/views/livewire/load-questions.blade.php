<div>
    <ul>
        @foreach ($questions as $question)
            <li>{{ $question->title }}</li>
        @endforeach
    </ul>
</div>
