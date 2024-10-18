<?php

namespace App\Filament\Resources\ExamResource\Pages;

use App\Models\Question;
use Filament\Resources\Pages\Page;
use App\Filament\Resources\ExamResource;

class AttachQuestions extends Page
{
    protected static string $resource = ExamResource::class;

    protected static string $view = 'filament.resources.exam-resource.pages.attach-questions';

    public $questionTitle;
    public $explanation;
    public $post;
    public $last_appeared;
    public $subject_id;
    public $date;
    public $subjects = [];
    public $correct_answer_index = null;
    public $options = [
        ['options' => '', 'is_correct' => false],
        ['options' => '', 'is_correct' => false],
        ['options' => '', 'is_correct' => false],
        ['options' => '', 'is_correct' => false],
    ];

    public function mount()
    {

        $this->subjects = \App\Models\Subject::all()->pluck('name', 'id');
    }

    public function setCorrectAnswer($index)
    {
        foreach ($this->options as $key => $option) {
            $this->options[$key]['is_correct'] = $key == $index;
        }
    }

    public function submit()
    {

        // Create the question
        $question = Question::create([
            'title' => $this->questionTitle,
            'post' => $this->post,
            'last_appeared' => $this->last_appeared,
            'subject_id' => $this->subject_id,
            'date' => $this->date,
            'explanation' => $this->explanation,
            'options' => $this->options,
        ]);
    }
}
