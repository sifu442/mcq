<?php

namespace App\Filament\Resources;

use App\Models\Exam;
use Filament\Tables;
use App\Models\Course;
use Filament\Forms\Form;
use App\Models\Enrollment;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
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
                Section::make()
                    ->columns(3)
                    ->schema([
                        Select::make('user_id')
                        ->relationship('user', 'name')
                        ->disabled(),
                        Select::make('course_id')
                            ->relationship('course', 'title')
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
                            ->columnSpanFull()
                            ->nullable(),
                            ]),
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
                                ->afterStateUpdated(function ($state, callable $set) {
                                    if ($state) {
                                        $nextDay = \Carbon\Carbon::parse($state)->addDay();
                                        $set('end_time', $nextDay->format('Y-m-d'));
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
