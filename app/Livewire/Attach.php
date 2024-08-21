<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Question;

class Attach extends Component
{
    public $search = '';
    public $selectedQuestion = null;
    public $questions = [];

    public function updatedSearch($value)
    {
        // Search for questions matching the search text
        $this->questions = Question::where('title', 'like', '%' . $value . '%')->get();
    }

    public function selectQuestion($questionId)
    {
        // Find the selected question and update the text input
        $this->selectedQuestion = Question::find($questionId);
        $this->search = $this->selectedQuestion->title;
        $this->questions = []; // Clear the list after selection
    }

    public function render()
    {
        return view('livewire.attach');
    }

}
