<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Question;

class SearchQuestion extends Component
{
    public $state = '';
    public $searchResults = [];

    public function updatedState($value)
    {
        $this->searchResults = Question::where('content', 'like', "%{$value}%")
            ->get(['id', 'title', 'content'])
            ->toArray();
    }

    public function setState($content)
    {
        $this->state = $content;
        $this->dispatchBrowserEvent('set-editor-content', ['content' => $this->state]);
    }

    public function render()
    {
        return view('livewire.search-question');
    }
}
