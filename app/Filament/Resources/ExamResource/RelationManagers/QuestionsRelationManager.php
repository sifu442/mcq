<?php

namespace App\Filament\Resources\ExamResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Subject;
use App\Models\Question;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Actions\AttachAction;
use Filament\Resources\RelationManagers\RelationManager;

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('subject_id')
                ->relationship('subject', 'name')
                ->createOptionForm([
                    TextInput::make('name')->required()
                ])
                ->required(),
            TextInput::make('exam_name'),
            TextInput::make('post'),
            DatePicker::make('date'),
            RichEditor::make('title')->required()->maxLength(255)->columnSpanFull(),
            Repeater::make('options')
                ->required()
                ->deletable(false)
                ->defaultItems(4)
                ->maxItems(4)
                ->schema([
                    TextInput::make('option'),
                    Checkbox::make('is_correct')->fixIndistinctState()->label('Correct Answer')
                ])
                ->columnSpanFull(),
            RichEditor::make('explanation')->columnSpanFull()
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('index')
                    ->label('Index')
                    ->getStateUsing(function ($rowLoop, $livewire): string {
                        $currentPage = method_exists($livewire, 'currentPage') ? $livewire->currentPage() : 1;
                        return (string) ($rowLoop->iteration + $livewire->tableRecordsPerPage * ($currentPage - 1));
                    }),
                TextColumn::make('title'),
            ])
            ->filters([
                // Add your filters here
            ])
            ->headerActions([AttachAction::make()
            ->recordSelect(
                fn (Select $select) => $select->createOptionForm([
                    Select::make('subject_id')
                    ->relationship('subject', 'name')
                    ->createOptionForm([
                        TextInput::make('name')->required()
                    ])
                    ->createOptionAction(function (Action $action) {
                        $action->mutateFormDataUsing(function (array $data) {
                            $data['slug'] = Str::slug($data['title']);

                            return $data;
                        });
                    })
                    ->required(),
                    TextInput::make('exam_name'),
                    TextInput::make('post'),
                    DatePicker::make('date'),
                    RichEditor::make('title')->required()->maxLength(255)->columnSpanFull(),
                    Repeater::make('options')
                        ->required()
                        ->deletable(false)
                        ->defaultItems(4)
                        ->maxItems(4)
                        ->schema([
                            TextInput::make('option'),
                            Checkbox::make('is_correct')->fixIndistinctState()->label('Correct Answer')
                        ])
                        ->columnSpanFull(),
                    RichEditor::make('explanation')->columnSpanFull()
                ]),
            )
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ])
            ]);
    }
}
