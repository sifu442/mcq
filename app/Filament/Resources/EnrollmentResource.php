<?php

namespace App\Filament\Resources;

use App\Models\Exam;
use Filament\Tables;
use App\Models\Course;
use Filament\Forms\Form;
use App\Models\Enrollment;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\DeleteAction;
use App\Filament\Resources\EnrollmentResource\Pages;

class EnrollmentResource extends Resource
{
    protected static ?string $model = Enrollment::class;

    protected static ?string $modelLabel = 'Enrollment & Routine';

    protected static ?string $pluralModelLabel ='Enrollments & Routines';

    protected static ?string $navigationIcon = 'heroicon-c-user-plus';

    public static function canCreate(): bool
   {
      return false;
   }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                        Select::make('user_id')
                        ->relationship('user', 'name')
                        ->disabled(),
                        DatePicker::make('enrolled_at')
                            ->disabled(),
                        Select::make('starts_from')
                            ->options(function (callable $get) {
                                $courseId = $get('course_id');

                                // Check if a course ID is available
                                if ($courseId) {
                                    // Get all exams related to the course
                                    $exams = Course::find($courseId)
                                        ->exams()
                                        ->select('exams.id', 'exams.name')
                                        ->pluck('name', 'id')
                                        ->toArray();

                                    return $exams;
                                }

                                return [];
                            })
                            ->native(false)
                            ->nullable(),
                            Select::make('course_id')
                            ->relationship('course', 'title')
                            ->disabled(),
                        Repeater::make('routine')
                            ->schema([
                                Select::make('exam_id')
                                    ->label('Exam Name')
                                    ->disabledOn('edit')
                                    ->options(Exam::pluck('name', 'id')->toArray())
                                    ->native(false),
                                DatePicker::make('start_time')
                                ->native(false)
                                ->live()
                                ->afterStateUpdated(function ($state, callable $get, callable $set, $component) {
                                    if ($state) {
                                        $courseId = $get('../../course_id');
                                        $course = Course::find($courseId);
                                        $gap = $course->gap;
                                        $participation_time = $course->participation_time;

                                        // Get the state of the entire repeater
                                        $repeaterState = $get('../../routine');

                                        // Ensure that the repeater state is an array
                                        if (!is_array($repeaterState)) {
                                            Log::error('Repeater state is not an array:', ['repeaterState' => $repeaterState]);
                                            return;
                                        }

                                        // Convert the repeater state to have numeric indices if needed
                                        $repeaterState = array_values($repeaterState);

                                        // Log the repeater state to debug its structure
                                        Log::info('Repeater state:', ['repeaterState' => $repeaterState]);

                                        // Find the index of the current item by matching the state
                                        $currentIndex = collect($repeaterState)->search(function ($item) use ($state) {
                                            return isset($item['start_time']) && $item['start_time'] === $state;
                                        });

                                        // Check if current index was found
                                        if ($currentIndex === false) {
                                            Log::error('Current item index not found in the repeater state.');
                                            return;
                                        }

                                        // Ensure that $currentIndex is an integer
                                        $currentIndex = intval($currentIndex);

                                        // Log the current repeater index
                                        Log::info('Current repeater index:', ['index' => $currentIndex]);

                                        // Calculate the new end_time for the current exam
                                        $endTime = \Carbon\Carbon::parse($state)->addHours($participation_time);
                                        $set('end_time', $endTime->format('Y-m-d'));

                                        // Adjust subsequent exams' start_time based on the current updated start_time
                                        $currentExamStartTime = \Carbon\Carbon::parse($state);
                                        for ($i = $currentIndex + 1; $i < count($repeaterState); $i++) {
                                            // Increment the start time for each subsequent exam
                                            $nextExamStartTime = $currentExamStartTime->copy()->addDays($gap);
                                            $repeaterState[$i]['start_time'] = $nextExamStartTime->format('Y-m-d');

                                            // Optionally, adjust the end_time too
                                            $nextExamEndTime = $nextExamStartTime->copy()->addHours($participation_time);
                                            $repeaterState[$i]['end_time'] = $nextExamEndTime->format('Y-m-d');

                                            // Log the updates for the next exam
                                            Log::info('Updated subsequent exam:', [
                                                'repeater_index' => $i,
                                                'start_time' => $nextExamStartTime->format('Y-m-d'),
                                                'end_time' => $nextExamEndTime->format('Y-m-d'),
                                            ]);

                                            // Update the currentExamStartTime for the next iteration
                                            $currentExamStartTime = $nextExamStartTime;
                                        }

                                        // Set the updated state for the repeater
                                        $component->state($repeaterState);
                                    }
                                }),
                                DatePicker::make('end_time')
                                ->live()
                                ->native(false),

                            ])
                            ->live()
                            ->addable(false)
                            ->columns(3)
                            ->deletable(false)
                            ->reorderable(false)
                            ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.id')
                    ->label('Roll')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Student Name')
                    ->searchable(),
                TextColumn::make('course.title')->label('Course Title'),
                TextColumn::make('enrolled_at')
                    ->date()
                    ->label('Enrolled At')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->requiresConfirmation()
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
            'index' => Pages\ListEnrollments::route('/'),
            'create' => Pages\CreateEnrollment::route('/create'),
            'edit' => Pages\EditEnrollment::route('/{record}/edit'),
        ];
    }
}
