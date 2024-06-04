<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Payment;
use Filament\Forms\Form;
use App\Models\Enrollment;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PaymentResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PaymentResource\RelationManagers;
use Illuminate\Support\Facades\DB;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            //
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('User Name'),
                TextColumn::make('payment_method'),
                TextColumn::make('phone_number'),
                TextColumn::make('course.title')->label('Course'),
                TextColumn::make('status'),
                TextColumn::make('created_at')->dateTime()
                ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->action(function (Payment $record) {
                        DB::transaction(function () use ($record) {
                            $record->update(['status' => 'Approved']);

                            Enrollment::create([
                                'user_id' => $record->user_id,
                                'course_id' => $record->course_id,
                                'enrolled_at' => now(), // Or $record->updated_at if you prefer to use the exact timestamp when status was updated
                            ]);
                        });
                    })
                    ->visible(function (Payment $record) {
                        return !in_array($record->status, ['Approved', 'Rejected']);
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->action(function (Payment $record) {
                        $record->update(['status' => 'Rejected']);
                    })
                    ->visible(function (Payment $record) {
                        return !in_array($record->status, ['Approved', 'Rejected']);
                    }),
            ])
            ->filters([
                //
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
