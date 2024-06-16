<?php

namespace App\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ExamResponse;
use Filament\Resources\Resource;
use App\Filament\Resources\ExamResponsesResource\Pages;
use Filament\Tables\Columns\TextColumn;

class ExamResponsesResource extends Resource
{
    protected static ?string $model = ExamResponse::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->searchable(),
                TextColumn::make('exam.name')->searchable()
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
            'index' => Pages\ListExamResponses::route('/'),
            'create' => Pages\CreateExamResponses::route('/create'),
            'edit' => Pages\EditExamResponses::route('/{record}/edit'),
        ];
    }
}
