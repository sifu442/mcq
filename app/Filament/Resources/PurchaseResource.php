<?php
namespace App\Filament\Resources;

use Carbon\Carbon;
use App\Models\Purchase;
use App\Models\Enrollment;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\PurchaseResource\Pages;

class PurchaseResource extends Resource
{
    protected static ?string $model = Purchase::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('user.name')->label('User')->sortable(),
                TextColumn::make('course.title')->label('Course')->sortable(),
                TextColumn::make('payment_method')->sortable(),
                TextColumn::make('phone_number')->sortable(),
                TextColumn::make('amount')->sortable(),
                TextColumn::make('status')->sortable(),
                TextColumn::make('created_at')->label('Purchased At')->dateTime()->sortable()
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->action(function ($record) {
                        // Update the purchase status to approved
                        $record->update(['status' => 'approved']);
                    
                        // Get user and course details
                        $userId = $record->user_id;
                        $courseId = $record->course_id;
                        $enrolledAt = Carbon::now();
                    
                        $course = $record->course;
                        $exams = DB::table('course_exam')->where('course_id', $courseId)->get();
                    
                        $examRoutines = [];
                        $currentStartTime = $enrolledAt;
                    
                        foreach ($exams as $exam) {
                            $gapDays = $course->gap;
                            $participationHours = $course->participation_time;
                        
                            // Calculate start and end times for this exam
                            $startTime = $currentStartTime->addDays($gapDays);  // Directly modify currentStartTime
                            $endTime = $startTime->copy()->addHours($participationHours);  // end_time after participation time
                        
                            // Store the exam routine
                            $examRoutines[] = [
                                'exam_id' => $exam->exam_id,
                                'start_time' => $startTime->toDateTimeString(),
                                'end_time' => $endTime->toDateTimeString(),
                            ];
                        
                            // Update the start time for the next exam to the current end_time
                            $currentStartTime = $endTime;  // The next exam starts after this one ends
                        }
                    
                        // Create enrollment record with the generated exam routines
                        Enrollment::create([
                            'user_id' => $userId,
                            'course_id' => $courseId,
                            'enrolled_at' => $enrolledAt,
                            'routine' => $examRoutines,
                        ]);
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
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                ])
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
            'index' => Pages\ListPurchases::route('/'),
        ];
    }
}
