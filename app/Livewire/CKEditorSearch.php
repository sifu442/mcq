<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Question;

class CKEditorSearch extends Component
{
    public $query;
    public $results = [];

    protected $listeners = ['searchQueryUpdated'];

    public function searchQueryUpdated($query)
    {
        $this->query = $query;
        $this->search();
    }

    public function search()
    {
        $this->results = Question::where('title', 'like', "%{$this->query}%")->get();
    }

    public function render()
    {
        return view('livewire.ck-editor-search', [
            'results' => $this->results,
        ]);
    }
}
