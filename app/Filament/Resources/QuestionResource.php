<?php

namespace App\Filament\Resources;

use stdClass;
use Filament\Tables;
use App\Models\Subject;
use App\Models\Question;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\CreateRecord;
use App\Forms\Components\TinyMCEEditor;
use App\Filament\Resources\QuestionResource\Pages;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        $latestExam = Question::latest()->first();

        return $form->schema([
            Select::make('subject_id')
                ->relationship('subject', 'name')
                ->createOptionForm([
                    TextInput::make('name')->required()
                ])
                ->default($latestExam->subject_id)
                ->preload()
                ->required(),
            TextInput::make('previous_exam')
                ->label('Exam Name')
                ->default($latestExam->previous_exam),
            TextInput::make('post')
                ->default($latestExam->post),
            DatePicker::make('date')
                ->default($latestExam->date)
                ->native(false),
            TinyMCEEditor::make('title')
                ->columnSpanFull(),
            Repeater::make('options')
                ->required()
                ->deletable(false)
                ->defaultItems(4)
                ->maxItems(4)
                ->schema([
                    TextInput::make('options'),
                    Checkbox::make('is_correct')
                        ->fixIndistinctState()
                        ->name('Correct Answer'),
                ])
                ->columnSpanFull(),
            TinyMCEEditor::make('explanation')
                ->columnSpanFull()
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
                TextColumn::make('title')
                    ->searchable()
                    ->formatStateUsing(fn (string $state): HtmlString => new HtmlString($state)),
                TextColumn::make('subject.name')
                    ->searchable(),
                TextColumn::make('previous_exam')
                    ->searchable(),
                TextColumn::make('post')
                    ->searchable(),
                TextColumn::make('date')
                    ->date('d/m/Y')
                    ->searchable()
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

class CreateQuestion extends CreateRecord
{
    protected static string $resource = QuestionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function mount(): void
    {
        parent::mount();

        // Fetch the latest records
        $latestSubject = Subject::latest()->first();
        $latestQuestion = Question::latest()->first();

        // Set default values if available
        if ($latestSubject) {
            $this->form->fill([
                'subject_id' => $latestSubject->id,
            ]);
        }

        if ($latestQuestion) {
            $this->form->fill([
                'previous_exam' => $latestQuestion->previous_exam,
                'post' => $latestQuestion->post,
                'date' => $latestQuestion->date,
            ]);
        }
    }
}
