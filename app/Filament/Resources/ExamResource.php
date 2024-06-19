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

class ExamResource extends Resource
{
    protected static ?string $model = Exam::class;

    //protected static ?string $modelLabel = 'পরীক্ষা';
    //protected static ?string $pluralModelLabel = 'পরীক্ষাসমূহ';

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
                ->label('Select Course')
                ->required(),
            TextInput::make('duration')
                ->required()
                ->numeric()
                ->suffix('Minutes')
                ->translateLabel(),
            TextInput::make('gap')
                ->required()
                ->numeric()
                ->suffix('Days')
                ->translateLabel()
                ->default(3),
            TextInput::make('participation_time')
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
        ];
    }
}
