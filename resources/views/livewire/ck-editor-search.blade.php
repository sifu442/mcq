<!-- resources/views/livewire/ck-editor-search.blade.php -->
<ul>
    @foreach ($results as $result)
        <li>{{ $result->title }}</li>
    @endforeach
</ul>
