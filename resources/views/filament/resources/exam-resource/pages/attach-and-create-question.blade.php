<x-filament-panels::page>

<form wire:submit.prevent="save">
    {{ $this->form }}
    <div class="mt-4">
        <h3>Search Results:</h3>
        <ul>
            @foreach($searchResults as $result)
                <li wire:click="selectQuestion({{ $result->id }})">
                    {{ $result->title }}
                </li>
            @endforeach
        </ul>
    </div>
    <x-filament::button type="submit">
        Save Question
    </x-filament::button>
</form>

</x-filament-panels::page>
