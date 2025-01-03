<?php

namespace App\Filament\Resources;

use stdClass;
use App\Models\Exam;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Forms\Components\CKEditor;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\DeleteAction;
use App\Filament\Resources\ExamResource\Pages;
use App\Filament\Resources\ExamResource\RelationManagers\QuestionsRelationManager;
use Filament\Actions\Modal\Actions\Action;

class ExamResource extends Resource
{
    protected static ?string $model = Exam::class;

    protected static ?string $navigationGroup = 'Course Content';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function form(Form $form): Form
    {

        return $form->schema([
            TextInput::make('name')
                ->required()
                ->unique(ignoreRecord: true)
                ->translateLabel(),
            Select::make('course_id')
                ->relationship('courses', 'title')
                ->multiple()
                ->preload(5)
                ->label('Course'),
            TextInput::make('duration')
                ->required()
                ->numeric()
                ->default(30)
                ->suffix('Minutes')
                ->translateLabel(),
            Select::make('score')
                ->native(false)
                ->default('1')
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
                ->default('0.50')
                ->options([
                    '0.25' => '0.25',
                    '0.50' => '0.50',
                    '0.70' => '0.50',
                    '1.00' => '1.00' ,
                    '1.25' => '1.25',
                    '1.50' => '1.50',
                    '2.00' => '2.00',

                ]),
            TextInput::make('full_mark')
                ->required()
                ->numeric(),
            CKEditor::make('syllabus')
                ->required()
                ->translateLabel()
                ->columnSpanFull(),
        ]);

    }

    public static function table(Table $table): Table
    {
        return $table

            ->columns([
                TextColumn::make('index')->state(
                    static function (HasTable $livewire, stdClass $rowLoop): string {
                        return (string) (
                            $rowLoop->iteration +
                            ($livewire->getTableRecordsPerPage() * (
                                $livewire->getTablePage() - 1
                            ))
                        );
                    }
                ),
                TextColumn::make('id')->searchable(),
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('questions_count')->label('Number of Questions')->counts('questions')
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);

    }



    public static function getRelations(): array
    {

        return [
            QuestionsRelationManager::class
        ];

        return [QuestionsRelationManager::class];

    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExams::route('/'),
            'create' => Pages\CreateExam::route('/create'),
            'edit' => Pages\EditExam::route('/{record}/edit'),
            'attach' => Pages\AttachQuestions::route('/{record}/attach'),
        ];
    }
}
