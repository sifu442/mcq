<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ExamResponse;

class ExamResults extends Component
{
    public $examId;
    public $examResponses;

    public function mount($examId)
    {
        $this->examId = $examId;
        $this->examResponses = ExamResponse::with('user')->where('exam_id', $this->examId)->get();
    }

    public function render()
    {
        return view('livewire.exam-results')->layout('layouts.app');
    }
}
