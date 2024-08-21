<div>
    @if(!empty($searchResults))
        <ul class="search-results">
            @foreach($searchResults as $result)
                <li wire:click="selectResult('{{ $result['title'] }}')">{{ $result['title'] }}</li>
            @endforeach
        </ul>
    @endif
</div>
