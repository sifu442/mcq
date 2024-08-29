<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Exam;
use Filament\Forms\Form;
use App\Models\Enrollment;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use App\Filament\Resources\EnrollmentResource\Pages;
use Filament\Forms\Components\TextInput;

class EnrollmentResource extends Resource
{
    protected static ?string $model = Enrollment::class;

    protected static ?string $modelLabel = 'Enrollment & Routine';

    protected static ?string $pluralModelLabel ='Enrollments & Routines';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function canCreate(): bool
   {
      return false;
   }

    public static function form(Form $form): Form
    {
        $examsForStartsFrom = Exam::pluck('name', 'id')->toArray();

        // Fetch exams separately for the routine repeater
        $examsForRoutine = Exam::pluck('name', 'id')->toArray();
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
                            ->options($examsForStartsFrom)
                            ->native(false)
                            ->columnSpanFull(),
                            ]),

                        Repeater::make('routine')
                            ->schema([
                                Select::make('exam_id')
                                    ->disabled()
                                    ->options($examsForRoutine)
                                    ->native(false),
                                DatePicker::make('start_time'),
                                DatePicker::make('end_time'),
                            ])
                            ->live()
                            ->addable(false)
                            ->columns(3)
                            ->deletable(false)
                            ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('Student Name'),
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
            'index' => Pages\ListEnrollments::route('/'),
            'create' => Pages\CreateEnrollment::route('/create'),
            'edit' => Pages\EditEnrollment::route('/{record}/edit'),
        ];
    }
}
