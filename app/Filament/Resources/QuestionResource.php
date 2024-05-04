<?php

namespace App\Filament\Resources;

use stdClass;
use Filament\Forms;
use Filament\Tables;
use App\Models\Subject;
use App\Models\Question;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\QuestionResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\QuestionResource\RelationManagers;
use Filament\Forms\Components\Repeater;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {

        return $form->schema([
            TextInput::make('title')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),
            Select::make('subject_id')
                ->relationship('subject', 'name')
                ->createOptionForm([TextInput::make('name')->required()])
                ->required(),
            TagsInput::make('last_appeared'),
            Repeater::make('options')
                ->required()
                ->deletable(false)
                ->defaultItems(4)
                ->maxItems(4)
                ->reorderable(false)
                ->simple(TextInput::make('options')->label('Option'))
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')->state(static function (HasTable $livewire, stdClass $rowLoop): string {
                    return (string) ($rowLoop->iteration + $livewire->getTableRecordsPerPage() * ($livewire->getTablePage() - 1));
                }),
                TextColumn::make('title')->searchable(),
                TextColumn::make('subject.name')->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([Tables\Actions\EditAction::make()])
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
            'index' => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'edit' => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }
}
