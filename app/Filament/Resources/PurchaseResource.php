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
use Illuminate\Support\Facades\Log;

class PurchaseResource extends Resource
{
    protected static ?string $model = Purchase::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                        $record->update(['status' => 'approved']);

                        $userId = $record->user_id;
                        $courseId = $record->course_id;
                        $enrolledAt = Carbon::now();

                        $exams = DB::table('course_exam')->where('course_id', $courseId)->get();
                        $examRoutines = [];
                        $currentStartTime = $enrolledAt;

                        foreach ($exams as $index => $exam) {
                            $gapDays = DB::table('exams')->where('id', $exam->exam_id)->value('gap');
                            $participationHours = DB::table('exams')->where('id', $exam->exam_id)->value('participation_time');
                            $startTime = $currentStartTime->copy()->addDays($gapDays);
                            $endTime = $startTime->copy()->addHours($participationHours);

                            $examRoutines[] = [
                                'exam_id' => $exam->exam_id,
                                'start_time' => $startTime->toDateTimeString(),
                                'end_time' => $endTime->toDateTimeString(),
                            ];

                            $currentStartTime = $startTime;
                        }

                        // Log the exam routines to verify the content
                        Log::info('Exam Routines', ['exam_routines' => $examRoutines]);

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
