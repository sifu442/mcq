<div>
    <input type="text" wire:model="search" placeholder="Search for a question..." class="form-input">
    @if(!empty($questions))
        <ul class="absolute z-10 w-full bg-white border border-gray-300">
            @foreach($questions as $question)
                <li wire:click="selectQuestion({{ $question->id }})" class="p-2 cursor-pointer hover:bg-gray-200">{{ $question->title }}</li>
            @endforeach
        </ul>
    @endif
</div>
