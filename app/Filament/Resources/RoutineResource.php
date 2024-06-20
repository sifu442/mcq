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
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\Resources\RoutineResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RoutineResource\RelationManagers;

class RoutineResource extends Resource
{
    protected static ?string $model = Routine::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        $exams = Exam::all(); // Retrieve all exams

        return $form
            ->schema([
                Section::make()
                    ->columns([
                        'lg' => 3
                    ])
                    ->schema([
                        Repeater::make('exam_routines')
                            ->label('Exam Routines')
                            ->recordActions([
                                function (Form $form) use ($exams) {
                                    return $form
                                        ->schema([
                                            Select::make('exam_id')
                                                ->label('Exam')
                                                ->options($exams->pluck('name', 'id')->toArray()) // Assuming 'name' is the attribute to display
                                                ->required(),
                                            DateTimePicker::make('start_time')
                                                ->label('Start Time')
                                                ->required(),
                                            DateTimePicker::make('end_time')
                                                ->label('End Time')
                                                ->required(),
                                        ]);
                                },
                            ])
                            ->records(
                                $exams->map(function ($exam) {
                                    return [
                                        'exam_id' => $exam->id,
                                    ];
                                })
                            )
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
