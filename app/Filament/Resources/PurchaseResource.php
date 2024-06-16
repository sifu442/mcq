<?php

namespace App\Filament\Resources;

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

                        // Enroll user into the course
                        $user = $record->user;
                        $course = $record->course;
                        $user->courses()->syncWithoutDetaching($course);

                        $enrollment = new Enrollment([
                            'user_id' => $record->user_id,
                            'course_id' => $record->course_id,
                            'enrolled_at' => now(),
                        ]);
                        $enrollment->save();
                    })
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->requiresConfirmation()
                    ->visible(fn($record) => $record->status === 'pending'),

                Action::make('reject')
                    ->label('Reject')
                    ->action(function ($record) {
                        $record->update(['status' => 'rejected']);
                    })
                    ->color('danger')
                    ->icon('heroicon-s-x-mark')
                    ->requiresConfirmation()
                    ->visible(fn($record) => $record->status === 'pending'),
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
