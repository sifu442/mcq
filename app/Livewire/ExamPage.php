<?php
namespace App\Livewire;

use App\Models\Exam;
use Livewire\Component;
use App\Models\ExamResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ExamPage extends Component
{
    public $exam;
    public $examId;
    public $duration;
    public $answers = [];
    public $wrongCount = 0;
    public $totalScore = 0;
    public $correctCount = 0;
    public $selectedCount = 0;
    public $unansweredCount = 0;
    public $examSubmitted = false;

    public function mount($examId)
    {
        $this->examId = $examId;
        $this->loadExam();
    }

    public function loadExam()
    {
        $user = Auth::user();

        $this->exam = Exam::with('questions')->find($this->examId);

        if (!$this->exam) {
            abort(404, 'Exam not found');
        }

        if (
            ExamResponse::where('user_id', $user->id)
                ->where('exam_id', $this->examId)
                ->exists()
        ) {
            abort(403, 'You have already submitted this exam.');
        }

        $this->duration = $this->exam->duration;
    }

    public function submitExam()
    {
        if ($this->examSubmitted) {
            return;
        }

        $user = Auth::user();

        if ($this->exam) {

            $this->totalScore = 0;
            $correctCount = 0;
            $wrongCount = 0;
            $unansweredCount = 0;
            $totalAnswered = 0;
            $responseData = [];

            foreach ($this->exam->questions as $question) {
                $userAnswer = $this->answers[$question->id] ?? null;


                $correctOptionField = 'option_' . strtolower($question->right_answer);
                $correctAnswer = $question->{$correctOptionField};


                $responseData[] = [
                    'question' => $question->title,
                    'options' => [
                        'A' => $question->option_a,
                        'B' => $question->option_b,
                        'C' => $question->option_c,
                        'D' => $question->option_d,
                    ],
                    'user_input' => $userAnswer,
                    'correct_answer' => $correctAnswer,
                ];

                if ($userAnswer === null) {

                    $unansweredCount++;
                    continue;
                }


                $totalAnswered++;


                if (trim($userAnswer) === trim($correctAnswer)) {
                    $correctCount++;
                    $this->totalScore += $this->exam->score;
                } else {
                    $wrongCount++;
                    $this->totalScore -= $this->exam->penalty;
                }
            }


            ExamResponse::create([
                'user_id' => $user->id,
                'exam_id' => $this->exam->id,
                'response_data' => $responseData,
                'total_score' => $this->totalScore,
                'correct_count' => $correctCount,
                'wrong_count' => $wrongCount,
                'unanswered_count' => $unansweredCount,
                'lost_points' => $wrongCount * $this->exam->penalty,
                'total_answered' => $totalAnswered,
            ]);
        }


        $this->examSubmitted = true;


        $this->reset(['answers']);


        return redirect()->route('exam-results', ['examId' => $this->examId]);
    }

    public function render()
    {
        return view('livewire.exam-page')->layout('layouts.app');
    }
}
