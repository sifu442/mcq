<?php

namespace App\Filament\Resources;

use stdClass;
use Filament\Forms;
use App\Models\Routine;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use App\Filament\Resources\RoutineResource\Pages;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\DateTimePicker;

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
                        Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->required(),
                        Select::make('course_id')
                            ->label('Course')
                            ->relationship('course', 'title')
                            ->required(),
                        Repeater::make('exams')
                            ->relationship('exams')  // Correct relationship type
                            ->schema([
                                Select::make('exam_id')
                                    ->label('Exam')
                                    ->relationship('exams', 'name')
                                    ->required(),
                                DateTimePicker::make('pivot.start_time')
                                    ->label('Start Time')
                                    ->required(),
                                DateTimePicker::make('pivot.end_time')
                                    ->label('End Time')
                                    ->required(),
                            ])
                            ->label('Exams')
                            ->columns([
                                'lg' => 2,
                            ]),
                    ]),
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
                TextColumn::make('exams.name')
                    ->label('Exam')
                    ->sortable(),
                TextColumn::make('exams.pivot.start_time')->dateTime()
                    ->label('Start Time')
                    ->sortable(),
                TextColumn::make('exams.pivot.end_time')->dateTime()
                    ->label('End Time')
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



