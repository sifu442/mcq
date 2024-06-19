<?php

namespace App\Filament\Resources;

use Log;
use Carbon\Carbon;
use Filament\Tables;
use App\Models\Purchase;
use App\Models\Enrollment;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\PurchaseResource\Pages;

class PurchaseResource extends Resource
{
    protected static ?string $model = Purchase::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([TextColumn::make('id')->sortable(), TextColumn::make('user.name')->label('User')->sortable(), TextColumn::make('course.title')->label('Course')->sortable(), TextColumn::make('payment_method')->sortable(), TextColumn::make('phone_number')->sortable(), TextColumn::make('amount')->sortable(), TextColumn::make('status')->sortable(), TextColumn::make('created_at')->label('Purchased At')->dateTime()->sortable()])
            ->filters([
                //
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->action(function ($record) {
                        $record->update(['status' => 'approved']);

                        // Enroll user in the course
                        $user = $record->user;
                        $course = $record->course;

                        // Assuming there's a many-to-many relationship between users and courses
                        $user->courses()->attach($course->id);

                        // Create the routine
                        $currentDate = Carbon::now();
                        $delayDays = 3; // Initial delay days
                        $exams = $course->exams;

                        foreach ($exams as $exam) {
                            $examDate = $currentDate->copy()->addDays($delayDays);

                            // Create a routine entry
                            Routine::create([
                                'user_id' => $user->id,
                                'course_id' => $course->id,
                                'exam_id' => $exam->id,
                                'exam_date' => $examDate,
                                'participation_time' => $exam->participation_time,
                            ]);

                            // Update delay days for the next exam
                            $delayDays += $exam->gap + ($exam->participation_time / 24);
                        }
                    })
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'pending'),

                Action::make('reject')
                    ->label('Reject')
                    ->action(function ($record) {
                        $record->update(['status' => 'rejected']);
                    })
                    ->color('danger')
                    ->icon('heroicon-s-x-mark')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'pending'),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
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
            'index' => Pages\ListPurchases::route('/'),
        ];
    }
}
