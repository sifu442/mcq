<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Question;

class QuestionSearch extends Component
{
    public $search = '';
    public $questions = [];
    public $selectedQuestion = null;

    public function updatedSearch()
    {
        if (strlen($this->search) > 2) {
            $this->questions = Question::where('title', 'like', '%' . $this->search . '%')->get();
        } else {
            $this->questions = [];
        }
    }

    public function selectQuestion($questionId)
    {
        $this->selectedQuestion = Question::find($questionId);
        $this->search = $this->selectedQuestion->title;
        $this->questions = [];
        $this->emit('questionSelected', $this->selectedQuestion);
    }

    public function render()
    {
        return view('livewire.question-search');
    }
}
