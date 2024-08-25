<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Exam;
use App\Models\Enrollment;
use App\Models\ExamResponse;
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
            $correctCount = 0;
            $wrongCount = 0;
            $unansweredCount = 0;
            $responseData = [];

            foreach ($this->exam->questions as $question) {
                if ($question && $question->options) {
                    $correctAnswer = collect($question->options)
                        ->where('is_correct', true)
                        ->pluck('options')
                        ->first();

                    $userAnswer = $this->answers[$question->id] ?? null;

                    $responseData[] = [
                        'question' => $question->title,
                        'options' => collect($question->options)
                            ->pluck('options')
                            ->toArray(),
                        'user_input' => $userAnswer,
                        'correct_answer' => $correctAnswer,
                    ];

                    if ($userAnswer === null) {
                        $unansweredCount++;
                    } elseif ($userAnswer === $correctAnswer) {
                        $correctCount++;
                    } else {
                        $wrongCount++;
                    }
                }
            }

            ExamResponse::create([
                'user_id' => $user->id,
                'exam_id' => $this->exam->id,
                'response_data' => $responseData,
                'total_score' => $correctCount * $this->exam->score - $wrongCount * $this->exam->penalty,
                'answered_correctly' => $correctCount,
                'answered_wrong' => $wrongCount,
                'unanswered' => $unansweredCount,
                'lost_points' => $wrongCount * $this->exam->penalty,
            ]);

            $this->correctCount = $correctCount;
            $this->wrongCount = $wrongCount;
            $this->unansweredCount = $unansweredCount;
        }

        $this->examSubmitted = true;

        // Redirect to the exam results page
        return redirect()->route('exam-results', ['examId' => $this->examId]);
    }

    public function updatedAnswers($value, $questionId)
    {
        // Reset counts before recalculating
        $this->correctCount = 0;
        $this->wrongCount = 0;
        $this->unansweredCount = 0;

        foreach ($this->exam->questions as $question) {
            if ($question && $question->options) {
                $correctAnswer = collect($question->options)
                    ->where('is_correct', true)
                    ->pluck('options')
                    ->first();
                $userAnswer = $this->answers[$question->id] ?? null;

                if ($userAnswer === null) {
                    $this->unansweredCount++;
                } elseif ($userAnswer === $correctAnswer) {
                    $this->correctCount++;
                } else {
                    $this->wrongCount++;
                }
            }
        }

        // Update the selected and unanswered counts for UI
        $this->selectedCount = count(array_filter($this->answers));
        $this->unansweredCount = $this->exam->questions->count() - $this->selectedCount;
    }

    public function render()
    {
        return view('livewire.exam-page')->layout('layouts.app');
    }
}
