<?php

namespace App\Filament\Resources\ExamResource\Pages;

use App\Models\Exam;
use App\Models\Question;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use App\Filament\Resources\ExamResource;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use AmidEsfahani\FilamentTinyEditor\TinyEditor;

class ListExams extends ListRecords
{
    protected static string $resource = ExamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('marge-exam')
                ->color('info')
                ->form([
                    Select::make('exam_ids')
                        ->options(Exam::all()->pluck('id', 'id'))
                        ->multiple(),
                    TextInput::make('name')
                        ->required()
                        ->translateLabel(),
                    Select::make('course_id')
                        ->relationship('course', 'title')
                        ->required(),
                    TextInput::make('duration')
                        ->required()
                        ->numeric()
                        ->suffix('Minutes')
                        ->translateLabel(),
                    TextInput::make('delay_days')
                        ->required()
                        ->numeric()
                        ->suffix('Days')
                        ->translateLabel(),
                    TextInput::make('available_for_hours')
                        ->required()
                        ->numeric()
                        ->suffix('Hours')
                        ->translateLabel(),
                    Select::make('score')
                        ->required()
                        ->options([
                            '1' => '1',
                            '2' => '2',
                            '3' => '3',
                            '2' => '2' ,
                            '3' => '3' ,
                            '4' => '4' ,
                            '5' => '5' ,
                            '6' => '6' ,
                            '7' => '7' ,
                            '8' => '8' ,
                            '9' => '9' ,
                            '10' =>'10',
                        ]),
                    Select::make('penalty')
                        ->required()
                        ->options([
                            '0.25' => '0.25',
                            '0.50' => '0.50',
                            '0.70' => '0.50',
                            '1.00' => '1.00' ,
                            '1.25' => '1.25',
                            '1.50' => '1.50',
                            '2.00' => '2.00',
                            ]),
                    TinyEditor::make('syllabus')
                        ->required()
                        ->translateLabel()
                        ->columnSpanFull(),
                        ])
                ->action(function (array $data) {
                    $newExam = Exam::create([
                        'name' => $data['name'],
                        'course_id' => $data['course_id'],
                        'duration' => $data['duration'],
                        'delay_days' => $data['delay_days'],
                        'available_for_hours' => $data['available_for_hours'],
                        'score' => $data['score'],
                        'penalty' => $data['penalty'],
                        'syllabus' => $data['syllabus'],
                    ]);

                    // Get questions from selected exams
                    $examIds = $data['exam_ids'];
                    $questions = Question::whereHas('exams', function ($query) use ($examIds) {
                        $query->whereIn('exam_id', $examIds);
                    })->get();

                    $newExam->questions()->attach($questions->pluck('id'));

                    return $newExam;
                    })
        ];
    }
}
