<x-app-layout>
    <h1 class="text-center mb-4">Notices</h1>

        @if($notices->isEmpty())
            <p class="text-center">No notices available.</p>
        @else
            <div class="list-group">
                @foreach($notices as $notice)
                    <div class="list-group-item list-group-item-action">
                        <h5 class="mb-1">{{ $notice->title }}</h5>
                        <p>{!! $notice->description !!}</p>
                    </div>
                @endforeach
            </div>
        @endif
</x-app-layout>
