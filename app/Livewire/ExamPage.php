<?php
namespace App\Livewire;

use Log;
use Carbon\Carbon;
use App\Models\Exam;
use Livewire\Component;
use App\Models\Enrollment;
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
    public $totalScore = 0;
    public $correctCount = 0;
    public $wrongCount = 0;
    public $unansweredCount = 0;
    public $lostPoints = 0;

    public function mount($examId)
    {
        $this->examId = $examId;
        $this->loadExam();
    }

    public function loadExam()
    {
        $user = Auth::user();

        // Find the enrollment for the course that has the exam
        $enrollment = Enrollment::where('user_id', $user->id)
            ->whereHas('course.exams', function ($query) {
                $query->where('exams.id', $this->examId);
            })
            ->first();

        // Load the exam details including questions
        $this->exam = Exam::with('questions')->find($this->examId);

        if (!$this->exam) {
            abort(404, 'Exam not found');
        }

        // Check if the user has already submitted the exam
        $examResponse = ExamResponse::where('user_id', $user->id)
            ->where('exam_id', $this->examId)
            ->first();
        if ($examResponse) {
            abort(403, 'You have already submitted this exam.');
        }

        $this->duration = $this->exam->duration;
        $this->unansweredCount = $this->exam->questions->count(); // Initialize unanswered count
    }

    public function submitExam()
{
    if ($this->examSubmitted) {
        return;
    }

    $user = Auth::user();

    if ($this->exam) {
        $responseData = [];
        $totalScore = 0;

        foreach ($this->exam->questions as $question) {
            // Check if the question object and its options exist
            if ($question && $question->options) {
                $correctAnswer = collect($question->options)->where('is_correct', true)->pluck('options')->first();
                $userAnswer = $this->answers[$question->id] ?? null;
                $isCorrect = $userAnswer === $correctAnswer;

                $options = collect($question->options)->pluck('options')->toArray();

                $responseData[] = [
                    'question' => $question->title,
                    'options' => $options,
                    'user_input' => $userAnswer,
                    'correct_answer' => $correctAnswer,
                ];

                if ($isCorrect) {
                    $totalScore += $this->exam->score;
                } elseif ($userAnswer !== null) {
                    $totalScore -= $this->exam->penalty;
                }
            } else {
                // Log or handle cases where the question or options are null
                Log::warning('Question or options are null for question ID: ' . $question->id);
            }
        }

        ExamResponse::create([
            'user_id' => $user->id,
            'exam_id' => $this->exam->id,
            'response_data' => $responseData,
            'total_score' => $totalScore,
            'answered_correctly' => $this->calculateAnsweredCorrectly(),
            'answered_wrong' => $this->calculateAnsweredWrong(),
            'unanswered' => $this->calculateUnanswered(),
            'lost_points' => $this->calculateLostPoints(),
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
