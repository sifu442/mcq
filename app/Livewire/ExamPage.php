<?php

namespace App\Livewire;

use App\Models\Exam;
use App\Models\Enrollment;
use App\Models\ExamResponse;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ExamPage extends Component
{
    public $exam;
    public $examId;
    public $answers = [];
    public $examResults = [];
    public $examSubmitted = false;
    public $duration;
    public $totalScore = 0;

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

        // if (!$enrollment) {
        //     abort(403, 'You are not enrolled in this course or this exam is not part of your routine.');
        // }

        // // Check if the exam is in the user's routine
        // $examInRoutine = collect($enrollment->routine)->firstWhere('exam_id', $this->examId);

        // if (!$examInRoutine) {
        //     abort(403, 'This exam is not part of your routine.');
        // }

        // Check exam availability using start_time from the routine
        //$examStartTime = Carbon::parse($examInRoutine['start_time']);

        // if (Carbon::now()->lt($examStartTime)) {
        //     abort(403, 'This exam is not yet available.');
        // }

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
    }

    public function submitExam()
    {
        if ($this->examSubmitted) {
            return;
        }

        $user = Auth::user();

        if ($this->exam) {
            $responseData = [];
            foreach ($this->exam->questions as $question) {
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
                    $this->totalScore += $this->exam->score;
                } elseif ($userAnswer !== null) {
                    $this->totalScore -= $this->exam->penalty;
                }
            }

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

    public function render()
    {
        return view('livewire.exam-page')->layout('layouts.app');
    }
}
