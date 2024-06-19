<?php

namespace App\Filament\Resources;

use stdClass;
use Filament\Forms;
use App\Models\Exam;
use Filament\Tables;
use App\Models\Routine;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Livewire\Component as Livewire;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\DatePicker;
use App\Filament\Resources\RoutineResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RoutineResource\RelationManagers;

class RoutineResource extends Resource
{
    protected static ?string $model = Routine::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns([
                        'lg' => 3
                    ])
                    ->schema([
                        Select::make('exam_id')
                            ->label('Exam')
                            ->relationship('exam', 'name')
                            ->live()
                            ->afterStateUpdated(function (Select $component) {
                                $examId = $component->getStatePath('exam_id.value');
                                if ($examId) {
                                    $exam = Exam::findOrFail($examId);
                                    $component->getParent()->getChildComponent('start_time')->value($exam->start_time);
                                    $component->getParent()->getChildComponent('end_time')->value($exam->end_time);
                                }
                            }),
                        DatePicker::make('start_time')
                            ->label('Start Time')
                            ->default(function (Forms\Get $get) {
                                $examId = $get('exam_id');
                                if ($examId) {
                                    $exam = \App\Models\Exam::find($examId);
                                    return $exam ? $exam->start_time : null;
                                }
                                return null;
                            }),
                        DatePicker::make('end_time')
                            ->label('End Time')
                            ->default(function (Forms\Get $get) {
                                $examId = $get('exam_id');
                                if ($examId) {
                                    $exam = \App\Models\Exam::find($examId);
                                    return $exam ? $exam->end_time : null;
                                }
                                return null;
                            }),
                    ])
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
                TextColumn::make('user.name')
                    ->sortable(),
                TextColumn::make('course.title')
                    ->sortable(),
                TextColumn::make('exam.name')
                    ->sortable(),
                TextColumn::make('start_time')->dateTime()
                    ->sortable(),
                TextColumn::make('end_time')->dateTime()
                    ->sortable(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoutines::route('/'),
            'create' => Pages\CreateRoutine::route('/create'),
            'edit' => Pages\EditRoutine::route('/{record}/edit'),
        ];
    }
}


