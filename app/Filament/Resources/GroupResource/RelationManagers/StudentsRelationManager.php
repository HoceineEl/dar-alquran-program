<?php

namespace App\Filament\Resources\GroupResource\RelationManagers;

use App\Models\Ayah;
use App\Models\Progress;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup as ActionsActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class StudentsRelationManager extends RelationManager
{
    protected static string $relationship = 'students';

    protected static ?string $title = 'الطلاب';

    protected static ?string $navigationLabel = 'الطلاب';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $modelLabel = 'طالب';

    protected static ?string $pluralModelLabel = 'طلاب';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('الاسم')
                    ->required(),
                Forms\Components\TextInput::make('phone')
                    ->label('رقم الهاتف')
                    ->default('06')
                    ->required(),
                Forms\Components\Select::make('sex')
                    ->label('الجنس')
                    ->options([
                        'male' => 'ذكر',
                        'female' => 'أنثى',
                    ])
                    ->default('male'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')->label('الاسم'),
                TextColumn::make('phone')
                    ->url(fn ($record) => "tel:{$record->phone}")
                    ->badge()
                    ->label('رقم الهاتف'),
                TextColumn::make('sex')->label('الجنس')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'male' => 'ذكر',
                            'female' => 'أنثى',
                        };
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->slideOver()
                    ->modalWidth('4xl'),
            ])
            ->actions([
                ActionsActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ViewAction::make(),
                ]),
                Tables\Actions\Action::make('progress')
                    ->label('التقدم اليومي')
                    ->icon('heroicon-o-chart-pie')
                    ->color('success')
                    ->modal()
                    ->slideOver()
                    ->form(function (Model $student) {
                        return self::progressForm($student);
                    })
                    ->action(function (array $data, Model $student) {
                        $data['created_by'] = auth()->id();
                        $data['student_id'] = $student->id;
                        Progress::create($data);
                        Notification::make('added')
                            ->title('تم إضافة التقدم بنجاح')
                            ->success()->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    public static function progressForm(Student $student): array
    {
        $student->load('group');
        $lastMemoProgress = Progress::with('student', 'ayah', 'student.group')
            ->where('student_id', $student->id)
            ->where('status', 'memorized')
            ->latest()
            ->first();
        $nextAyah = 1;
        $nextLinesFrom = 1;
        $groupType = $student->group->type;
        $linesPerAyah = $groupType === 'two_lines' ? 2 : 7;
        $nextLinesTo = $nextLinesFrom + $linesPerAyah;

        if ($lastMemoProgress) {
            $lastAyah = $lastMemoProgress->ayah;
            $nextAyah = $lastAyah->page_number;
            $nextLinesFrom = $lastMemoProgress->line_end + 1;
            $nextLinesTo = $nextLinesFrom + $linesPerAyah;

            $lastAyahLinesCount = $lastAyah->lines_count;
            if ($nextLinesTo > $lastAyahLinesCount) {
                $nextAyah += 1;
                $nextLinesFrom = 1;
                $nextLinesTo = $nextLinesFrom + $linesPerAyah;
            }
        }

        $ayah_id = Ayah::where('page_number', $nextAyah)->first()->id;
        $lines_from = $nextLinesFrom;
        $lines_to = $nextLinesTo;

        return [
            Grid::make(2)
                ->schema([
                    Placeholder::make('student_name')
                        ->label('الطالب')
                        ->content($student->name . ' - ' . $student->phone),
                    DatePicker::make('date')
                        ->label('التاريخ')
                        ->default(now())
                        ->displayFormat('Y-m-d')
                        ->required(),
                ]),
            ToggleButtons::make('status')
                ->label('الحالة')
                ->inline()
                ->reactive()
                ->icons([
                    'memorized' => 'heroicon-o-check-circle',
                    'absent' => 'heroicon-o-x-circle',
                ])
                ->grouped()
                ->default('memorized')
                ->colors([
                    'memorized' => 'primary',
                    'absent' => 'danger',
                ])
                ->options([
                    'memorized' => 'أتم الحفظ',
                    'absent' => 'غائب',
                ])
                ->required(),
            Section::make()
                ->columns(3)
                ->hidden(fn (Get $get) => $get('status') !== 'memorized')
                ->schema([
                    Select::make('ayah_id')
                        ->label('الصفحة')
                        ->options(fn () => Ayah::get()->mapWithKeys(fn (Ayah $ayah) => [$ayah->id => $ayah->page_number . ' - ' . $ayah->surah_name]))
                        ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->ayahName}")
                        ->preload()
                        ->default(fn () => $ayah_id)
                        ->reactive()
                        ->optionsLimit(700)
                        ->searchable()
                        ->required(),
                    Select::make('lines_from')
                        ->label('من السطر')
                        ->reactive()
                        ->default(fn () => $lines_from)
                        ->options(function (Get $get) {
                            $ayah = Ayah::find($get('ayah_id'));
                            if ($ayah) {
                                return range(1, $ayah->lines_count);
                            }
                            return range(1, 15);
                        })
                        ->required(),
                    Select::make('lines_to')
                        ->reactive()
                        ->options(function (Get $get) {
                            $ayah = Ayah::find($get('ayah_id'));
                            if ($ayah) {
                                return range(1, $ayah->lines_count);
                            }
                            return range(1, 15);
                        })
                        ->default(fn () => $lines_to)
                        ->label('إلى السطر')
                        ->required(),
                ]),
            MarkdownEditor::make('notes')
                ->label('ملاحظات')
                ->columnSpanFull()
                ->placeholder('أدخل ملاحظاتك هنا'),
        ];
    }
}
