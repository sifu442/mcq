<?php

namespace App\Filament\Resources;


use Carbon\Carbon;
use Filament\Tables;
use App\Models\Routine;
use App\Models\Purchase;
use App\Models\Enrollment;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Log;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\PurchaseResource\Pages;
use App\Filament\Resources\PurchaseResource\Pages\ListPurchases;

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
