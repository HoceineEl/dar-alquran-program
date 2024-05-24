<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgressResource\Pages;
use App\Models\Ayah;
use App\Models\Progress;
use App\Models\Student;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ProgressResource extends Resource
{
    protected static ?string $model = Progress::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'التقدم اليومي';

    protected static ?string $modelLabel = 'تقدم يومي';

    protected static ?string $pluralModelLabel = 'تقدم يومي';

    protected static ?string $recordTitleAttribute = 'date';

    public static function form(Form $form): Form
    {

        return $form
            ->schema(
                self::progressForm(),
            );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.name')->label('الطالب')->sortable(),
                TextColumn::make('date')->label('التاريخ')->date(),
                TextColumn::make('status')->label('الحالة')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'memorized' => 'محفوظ',
                            'not_memorized' => 'غير محفوظ',
                            'absent' => 'غائب',
                        };
                    })->sortable(),
                TextColumn::make('ayah.page_number')->label('الصفحة')->sortable(),
                TextColumn::make('lines_from')->label('من السطر'),
                TextColumn::make('lines_to')->label('إلى السطر'),
                TextColumn::make('ayah.surah_name')->label('اسم السورة')->toggleable()->sortable(),
                TextColumn::make('createdBy.name')->label('أضيف بواسطة')->sortable(),
                TextColumn::make('created_at')->label('تاريخ الإضافة')->date(),
            ])
            ->searchable()
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListProgress::route('/'),
            'create' => Pages\CreateProgress::route('/create'),
            'edit' => Pages\EditProgress::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'student.name',
            'date',
            'ayah',
            'lines_from',
            'lines_to',
        ];
    }

    public static function progressForm(): array
    {
        return [
            Select::make('student_id')
                ->label('الطالب')
                ->searchable()
                ->preload()
                ->reactive()
                ->afterStateUpdated(function (Set $set, $state) {
                    $student = Student::find($state);
                    $lastMemoProgress = Progress::with('student', 'ayah', 'student.group')
                        ->where('student_id', $state)
                        ->where('status', 'memorized')
                        ->latest()
                        ->first();
                    $nextAyah = 1;
                    $nextLinesFrom = 1;
                    $groupType = $student->group->type;
                    $nextLinesTo = $groupType === 'two_lines' ? 3 : 8;

                    if ($lastMemoProgress) {
                        $lastAyah = $lastMemoProgress->ayah;
                        $nextAyah = $lastAyah->page_number;
                        $nextLinesFrom = $lastMemoProgress->lines_to + 1;
                        $nextLinesTo = $groupType === 'two_lines' ? $nextLinesFrom + 2 : $nextLinesFrom + 7;
                        if ($nextLinesTo > $lastAyah->lines_count) {
                            $nextAyah += 1;
                            $nextLinesFrom = 1;
                            $nextLinesTo = $groupType === 'two_lines' ? 3 : 8;
                        }
                    }

                    $set('ayah_id', $nextAyah);
                    $set('lines_from', $nextLinesFrom - 1);
                    $set('lines_to', $nextLinesTo - 1);
                })
                ->relationship('student', 'name')
                ->required(),
            DatePicker::make('date')
                ->label('التاريخ')
                ->default(now())
                ->displayFormat('Y-m-d')
                ->required(),
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
                        ->relationship('ayah', 'page_number')
                        ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->ayahName}")
                        ->preload()
                        ->reactive()
                        ->optionsLimit(700)
                        ->searchable()
                        ->required(),
                    Select::make('lines_from')
                        ->label('من السطر')
                        ->reactive()
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
