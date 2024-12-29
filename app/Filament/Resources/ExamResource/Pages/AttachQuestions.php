<?php
namespace App\Filament\Resources\ExamResource\Pages;

use App\Models\Exam;
use App\Models\Subject;
use App\Models\Question;
use Filament\Resources\Pages\Page;
use App\Filament\Resources\ExamResource;

class AttachQuestions extends Page
{
    protected static string $resource = ExamResource::class;
    protected static string $view = 'filament.resources.exam-resource.pages.attach-questions';

    public $id;
    public $post;
    public $date;
    public $topic;
    public $examId;
    public $option_a;
    public $option_b;
    public $option_c;
    public $option_d;
    public $subject_id;
    public $explanation;
    public $right_answer;
    public $previous_exam;
    public $subjects = [];
    public $questionTitle;
    public $searchTerm = '';
    public $searchResults = [];

    protected $listeners = [
        'updateTitle' => 'setTitle',
        'updateOptionA' => 'setOptionA',
        'updateOptionB' => 'setOptionB',
        'updateOptionC' => 'setOptionC',
        'updateOptionD' => 'setOptionD',
        'inputSearchTerm' => 'updateSearchTerm',
        'explanationUpdated' => 'updateExplanation',
        'replaceEditorContent' => 'replaceEditorContent',
        'updatePost',
        'updateTopic',
        'updateDate',
        'updateId',
        'updatePreviousExam',
    ];

    public function mount()
    {
        $this->examId = request()->route()->parameter('record');
        $this->subjects = Subject::latest()->take(5)->get();
    }

    public function updateOption($index, $content)
    {
        if (!isset($this->options[$index])) {
            $this->options[$index] = [];
        }

        $this->options[$index]['options'] = $content;
    }

    public function updatePost($post)
    {
        $this->post = $post;
    }

    public function updateTopic($topic)
    {
        $this->topic = $topic;
    }

    public function updateDate($date)
    {
        $this->date = $date;
    }

    public function updateId($id)
    {
        $this->id = $id;
    }

    public function setTitle($content)
    {
        $this->questionTitle = $content;
    }

    public function setOptionA($content)
    {
        $this->option_a = $content;
    }

    public function setOptionB($content)
    {
        $this->option_b = $content;
    }

    public function setOptionC($content)
    {
        $this->option_c = $content;
    }

    public function setOptionD($content)
    {
        $this->option_d = $content;
    }

    public function setRightAnswer($content)
    {
        $this->right_answer = $content;
    }

    public function updateExplanation($content)
    {
        $this->explanation = $content;
    }

    public function updateSearchTerm($content)
    {
        $this->searchTerm = trim(strip_tags($content));

        if (!empty($this->searchTerm)) {
            $this->searchResults = Question::where('title', 'like', '%' . $this->searchTerm . '%')
                ->take(5)
                ->get();
        } else {
            $this->searchResults = [];
        }
    }

    public function replaceEditorContent($content)
    {
        $this->dispatchBrowserEvent('replaceEditorContent', ['content' => $content]);
    }

    private function handleQuestionCreation()
    {
        $this->validate(
            [
                'questionTitle' => 'required',
                'subject_id' => 'required',
                'option_a' => 'required',
                'option_b' => 'required',
                'option_c' => 'required',
                'option_d' => 'required',
                'right_answer' => 'required',
            ],
            [
                'questionTitle.required' => 'This field must be filled.',
                'subject_id.required' => 'This field must be filled.',
                'option_a.required' => 'This field must be filled.',
                'option_b.required' => 'This field must be filled.',
                'option_c.required' => 'This field must be filled.',
                'option_d.required' => 'This field must be filled.',
                'right_answer.required' => 'This field must be filled.',
            ],
        );

        $question = null;

        if (!empty($this->id)) {
            $question = Question::find($this->id);
        }

        if (!$question) {
            $question = Question::create([
                'post' => $this->post,
                'topic' => $this->topic,
                'date' => $this->date,
                'option_a' => $this->option_a,
                'option_b' => $this->option_b,
                'option_c' => $this->option_c,
                'option_d' => $this->option_d,
                'title' => $this->questionTitle,
                'subject_id' => $this->subject_id,
                'explanation' => $this->explanation,
                'previous_exam' => $this->previous_exam,
                'right_answer' => $this->right_answer,
            ]);

            session()->flash('success', 'New question created successfully.');
        } else {
            session()->flash('info', 'Existing question found and attached.');
        }

        if ($this->examId) {
            $exam = Exam::find($this->examId);

            if ($exam) {
                $exam->questions()->syncWithoutDetaching([$question->id]);
                session()->flash('success', 'Question attached to the exam successfully.');
            } else {
                session()->flash('error', 'Exam not found. Question saved without attachment.');
            }
        } else {
            session()->flash('info', 'Exam ID not provided. Question saved without attachment.');
        }

        return $question;
    }

    public function submitForm()
    {
        $this->handleQuestionCreation();

        return redirect()->to("/admin/exams/{$this->examId}/edit");
    }

    public function createAnother()
    {
        $this->handleQuestionCreation();

        return redirect()->to("/admin/exams/{$this->examId}/attach");
    }
}
