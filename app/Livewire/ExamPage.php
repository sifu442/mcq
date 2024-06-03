<?php

namespace App\Livewire;
use App\Models\Exam;
use Livewire\Component;
use App\Models\ExamResponse;
use Illuminate\Support\Facades\Auth;

class ExamPage extends Component
{
    public $exam;
    public $examId;
    public $answers = [];
    public $examResults = [];
    public $examSubmitted = false;
    public $duration;
    public $score;
    public $penalty;
    public $totalScore = 0;

    public function mount($examId){
        $this->examId = $examId;
        $this->exam = Exam::with('questions')->find($this->examId);
        $this->duration = $this->exam->duration;
    }


    public function submitExam()
    {
        if ($this->examSubmitted) {
            return;
        }

        $user = Auth::user(); // Get the authenticated user

        if ($this->exam) {
            $responseData = [];
            foreach ($this->exam->questions as $question) {
                $correctAnswer = collect($question->options)->where('is_correct', true)->pluck('options')->first();
                $userAnswer = $this->answers[$question->id] ?? null;
                $isCorrect = $userAnswer === $correctAnswer;

                // Assuming options is a collection of arrays with 'options' and 'is_correct' keys
                $options = collect($question->options)->pluck('options')->toArray();

                $responseData[] = [
                    'question' => $question->title,
                    'options' => $options,
                    'user_input' => $userAnswer,
                    'correct_answer' => $correctAnswer,
                ];

                if ($isCorrect) {
                    $this->totalScore += $this->exam->score; // Increment score for correct answer
                } elseif ($userAnswer !== null) {
                    // Decrement score for wrong answer
                    $this->totalScore -= $this->exam->penalty;
                }
            }

            // Allow total score to be negative
            $totalScore = $this->totalScore;

            ExamResponse::create([
                'user_id' => $user->id,
                'exam_id' => $this->exam->id,
                'response_data' => $responseData,
                'total_score' => $totalScore,
            ]);
        }

        $this->examSubmitted = true;
        $this->reset(['answers']);

        return redirect()->route('exam-results', ['examId' => $this->examId]);
    }


    public function render(){
        return view('livewire.exam-page')->layout('layouts.app');
    }
}
