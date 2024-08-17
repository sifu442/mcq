<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Question;

class LoadQuestions extends Component
{
    public $search = '';

    public function render()
    {
        $questions = Question::where('title', 'like', '%' . $this->search . '%')->get();

        return view('livewire.load-questions', [
            'questions' => $questions,
        ]);
    }

    public function getListeners()
    {
        return [
            'updateSearch' => 'updateSearch',
        ];
    }

    public function updateSearch($search)
    {
        $this->search = $search;
    }
}
