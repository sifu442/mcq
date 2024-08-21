<div>
    <!-- Modal Trigger Button -->
    <button wire:click="$emit('openModal')">Open Search Modal</button>

    <!-- Modal -->
    <div class="modal" style="display: block;"> <!-- Use actual modal display logic -->
        <div class="modal-content">
            <input type="text" wire:model.debounce.300ms="search" placeholder="Search questions...">

            <ul>
                @foreach($questions as $question)
                    <li wire:click="selectQuestion({{ $question->id }})">{{ $question->title }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
