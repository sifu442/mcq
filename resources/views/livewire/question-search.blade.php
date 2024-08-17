<div>
    <input type="text" wire:model="searchTerm" placeholder="Search Questions..." class="block w-full border border-gray-300 p-2 mb-4" />

    <ul>
        @foreach($questions as $question)
            <li>{{ $question->title }}</li>
        @endforeach
    </ul>
</div>
