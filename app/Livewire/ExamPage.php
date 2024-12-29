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
            // Initialize counters
            $this->totalScore = 0; // Reset total score
            $correctCount = 0;
            $wrongCount = 0;
            $unansweredCount = 0;
            $totalAnswered = 0;
            $responseData = [];

            foreach ($this->exam->questions as $question) {
                $userAnswer = $this->answers[$question->id] ?? null; // Get user's answer for this question

                // Determine the correct option dynamically
                $correctOptionField = 'option_' . strtolower($question->right_answer); // e.g., option_c
                $correctAnswer = $question->{$correctOptionField}; // Get the actual text (HTML) of the correct option

                // Add question data to the response log
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
                    // Count unanswered questions
                    $unansweredCount++;
                    continue; // Skip further checks
                }

                // Increment total answered
                $totalAnswered++;

                // Check if the user's answer matches the correct answer (compare HTML content)
                if (trim($userAnswer) === trim($correctAnswer)) {
                    $correctCount++; // Increment correct answers count
                    $this->totalScore += $this->exam->score; // Add score for correct answer
                } else {
                    $wrongCount++; // Increment wrong answers count
                    $this->totalScore -= $this->exam->penalty; // Deduct penalty for wrong answer
                }
            }

            // Save the results to the database
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

        // Mark the exam as submitted
        $this->examSubmitted = true;

        // Reset answers to prevent re-submission
        $this->reset(['answers']);

        // Redirect to the results page
        return redirect()->route('exam-results', ['examId' => $this->examId]);
    }

    public function render()
    {
        return view('livewire.exam-page')->layout('layouts.app');
    }
}
