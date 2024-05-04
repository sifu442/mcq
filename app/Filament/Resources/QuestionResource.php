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
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\QuestionResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\QuestionResource\RelationManagers;
use Filament\Forms\Components\TagsInput;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;


    protected static ?string $modelLabel = 'প্রশ্ন';

    protected static ?string $pluralModelLabel = 'প্রশ্নসমূহ';

    //protected static ?string $modelLabel = 'প্রশ্ন';

    //protected static ?string $pluralModelLabel = 'প্রশ্নসমূহ';


    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('title')
                //->label('Select Subject')
                ->required()
                ->columnSpanFull(),
            Select::make('subject_id')
                ->relationship('subject', 'name')
                ->createOptionForm([
                    TextInput::make('name')->required()
                ])
                ->required(),
                TagsInput::make('last_appeared'),
            Repeater::make('options')
                ->required()
                ->deletable(false)
                ->defaultItems(4)
                ->maxItems(4)
                ->columnSpanFull()
                ->schema(function () {
                    $fields = [];
        $labels = ['A', 'B', 'C', 'D'];

        for ($i = 0; $i < 4; $i++) {
            $fields[] = TextInput::make('options_' . $i)
                ->label('Option ' . $labels[$i]);
        }

        $fields[] = Checkbox::make('is_correct')
            ->fixIndistinctState()
            ->name('Correct Answer');

        return $fields;
                }),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')->state(
                    static function (HasTable $livewire, stdClass $rowLoop): string {
                        return (string) (
                            $rowLoop->iteration +
                            ($livewire->getTableRecordsPerPage() * (
                                $livewire->getTablePage() - 1
                            ))
                        );
                    }
                ),
                TextColumn::make('title')->searchable(),
                TextColumn::make('subject.name')->searchable()
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