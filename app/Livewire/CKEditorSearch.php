<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Question;

class CKEditorSearch extends Component
{
    public $query = '';
    public $searchResults = [];

    public function updatedQuery()
    {
        if (strlen($this->query) > 2) {
            $this->searchResults = Question::where('title', 'like', '%' . $this->query . '%')->get()->toArray();
        } else {
            $this->searchResults = [];
        }
    }

    public function selectResult($result)
    {
        $this->emit('fillEditor', $result);
        $this->searchResults = [];
    }

    public function render()
    {
        return view('livewire.ck-editor-search');
    }
}
