<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Question;

class QuestionSearch extends Component
{
    public $searchTerm = '';

    public function render()
    {
        $questions = [];

        if ($this->searchTerm) {
            $questions = Question::where('title', 'like', '%' . $this->searchTerm . '%')->get();
        }

        return view('livewire.question-search', ['questions' => $questions]);
    }
}
