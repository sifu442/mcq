
    @if(count($this->searchResults) > 0)
        <div class="mt-4">
            <h3 class="text-lg font-medium">Search Results:</h3>
            <ul class="list-disc pl-5">
                @foreach($this->searchResults as $result)
                    <li>{{ $result }}</li>
                @endforeach
            </ul>
        </div>
    @endif


