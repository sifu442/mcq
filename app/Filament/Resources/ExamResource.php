<?php

namespace App\Filament\Resources;

use stdClass;
use Filament\Forms;
use App\Models\Exam;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TimePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ExamResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ExamResource\RelationManagers;
use App\Filament\Resources\ExamResource\RelationManagers\QuestionsRelationManager;
use App\Models\Subject;

class ExamResource extends Resource
{
    protected static ?string $model = Exam::class;

    //protected static ?string $modelLabel = 'পরীক্ষা';
    //protected static ?string $pluralModelLabel = 'পরীক্ষাসমূহ';


    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {

        return $form->schema([
            TextInput::make('name')->required()->translateLabel(),
            Select::make('course_id')->relationship('course', 'title')->required(),
            TextInput::make('duration')->required()->numeric()->suffix('Minutes')->translateLabel(),
            TextInput::make('delay_days')->required()->numeric()->suffix('Days')->translateLabel(),
            TextInput::make('available_for_hours')->required()->numeric()->suffix('Hours')->translateLabel(),
            Select::make('score')
            ->required()
            ->options([
                '1' => '1',
                '2' => '2',
                '3' => '3',
            ]),
            Select::make('penalty')
            ->required()
            ->options([
                '0.25' => '0.25',
                '0.50' => '0.50',
                '0.70' => '0.50',
                '1' => '1',
            ]),
            RichEditor::make('syllabus')->required()->translateLabel()->columnSpanFull(),
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
                TextColumn::make('id'),
                TextColumn::make('name')->sortable()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
