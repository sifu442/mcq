<?php

namespace App\Filament\Resources;

use stdClass;
use Filament\Tables;
use App\Models\Subject;
use App\Models\Question;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Forms\Components\CKEditor;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\QuestionResource\Pages;


class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static ?string $navigationGroup = 'Course Content';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        $latestExam = Question::latest()->first();

        return $form->schema([
            Select::make('subject_id')
                ->relationship('subject', 'name')
                ->createOptionForm([TextInput::make('name')->required()])
                ->preload()
                ->required()
                ->native(false)
                ->default($latestExam->subject_id ?? null),
            TextInput::make('previous_exam')
                ->label('Exam Name')
                ->default($latestExam->previous_exam ?? ''),
            TextInput::make('post')
                ->default($latestExam->post ?? ''),
            DatePicker::make('date')
                ->native(false)
                ->default($latestExam->date ?? '')
                ->firstDayOfWeek(6),
            TextInput::make('topic')
                ->default($latestExam->topic ?? ''),
            CKEditor::make('title')->required()->columnSpanFull()->default($latestExam->title ?? ''),
            Section::make('Options')
                ->schema([
                    CKEditor::make('option_a')->required()->columnSpanFull()->default($latestExam->otpion_a ?? ''),
                    CKEditor::make('option_b')->required()->columnSpanFull()->default($latestExam->otpion_b ?? ''),
                    CKEditor::make('option_c')->required()->columnSpanFull()->default($latestExam->otpion_c ?? ''),
                    CKEditor::make('option_d')->required()->columnSpanFull()->default($latestExam->otpion_d ?? ''),
                    Select::make('right_answer')
                        ->options([
                        'A' => 'A',
                        'B' => 'B',
                        'C' => 'C',
                        'D' => 'D',
                    ]),
                ]),
            CKEditor::make('explanation')->columnSpanFull()->default($latestExam->explanation ?? ''),
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
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
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
