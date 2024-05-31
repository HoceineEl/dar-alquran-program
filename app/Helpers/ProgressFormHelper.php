<?php

namespace App\Helpers;

use App\Models\Group;
use App\Models\Page;
use App\Models\Progress;
use App\Models\Student;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Model;

class ProgressFormHelper
{
    public static function getProgressFormSchema(?Student $student = null, Group $group = null): array
    {
        $progressData = $student ? self::calculateNextProgress($student) : null;
        $students = $group ? $group->students : Student::all();
        return [
            Grid::make(2)
                ->schema([
                    Placeholder::make('student_name')
                        ->label('الطالب')
                        ->hidden(fn () => !$student)
                        ->content($student ? $student->name . ' - ' . $student->phone : ''),

                    Select::make('student_id')
                        ->label('الطالب')
                        ->options(fn (Get $get) => $students->filter(function ($student) use ($get) {
                            return $student->progresses->where('date', $get('date'))->count() == 0;
                        })->mapWithKeys(fn (Student $student) => [$student->id => $student->name . ' - ' . $student->phone])->toArray())
                        ->preload()
                        ->reactive()
                        ->afterStateUpdated(function ($state, Set $set) {
                            $progressData = $state ? ProgressFormHelper::calculateNextProgress(Student::find($state)) : null;
                            $set('page_id', $progressData['page_id'] ?? null);
                            $set('lines_from', $progressData['lines_from'] ?? 1);
                            $set('lines_to', $progressData['lines_to'] ?? 1);
                        })
                        ->hidden(fn () => $student)
                        ->required(),
                    DatePicker::make('date')
                        ->label('التاريخ')
                        ->default(now())
                        ->reactive()
                        ->displayFormat('Y-m-d')
                        ->required(),
                ]),
            Grid::make(2)
                ->schema([
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
                    ToggleButtons::make('comment')
                        ->label('التعليق')
                        ->inline()
                        ->default('message_sent')
                        ->colors([
                            'message_sent' => 'success',
                            'call_made' => 'warning',
                        ])
                        ->options([
                            'message_sent' => 'تم إرسال الرسالة',
                            'voice_message_sent' => 'تم إرسال رسالة صوتية',
                            'call_made' => 'تم الاتصال',
                        ]),
                ]),
            Section::make()
                ->columns(3)
                ->hidden(fn (Get $get) => $get('status') !== 'memorized')
                ->schema([
                    Select::make('page_id')
                        ->label('الصفحة')
                        ->options(Page::all()->mapWithKeys(fn (Page $page) => [$page->id => "{$page->number} - {$page->surah_name}"])->toArray())
                        ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->number} - {$record->surah_name}")
                        ->preload()
                        ->default(fn () => $progressData['page_id'] ?? null)
                        ->reactive()
                        ->optionsLimit(700)
                        ->searchable()
                        ->required(),
                    Select::make('lines_from')
                        ->label('من السطر')
                        ->reactive()
                        ->default(fn () => $progressData['lines_from'] ?? 1)
                        ->options(function (Get $get) {
                            $page = Page::find($get('page_id'));
                            if ($page) {
                                return range(1, $page->lines_count);
                            }

                            return range(1, 15);
                        })
                        ->required(),
                    Select::make('lines_to')
                        ->reactive()
                        ->options(function (Get $get) {
                            $page = Page::find($get('page_id'));
                            if ($page) {
                                return range(1, $page->lines_count);
                            }

                            return range(1, 15);
                        })
                        ->default(fn () => $progressData['lines_to'] ?? 1)
                        ->label('إلى السطر')
                        ->required(),
                ]),
            MarkdownEditor::make('notes')
                ->label('ملاحظات')
                ->columnSpanFull()
                ->placeholder('أدخل ملاحظاتك هنا'),
        ];
    }

    public static function calculateNextProgress(Student $student): array
    {
        $student->load('group');
        $lastMemoProgress = Progress::with('student', 'page', 'student.group')
            ->where('student_id', $student->id)
            ->where('status', 'memorized')
            ->latest()
            ->first();

        $nextPageNumber = 1;
        $nextLinesFrom = 1;
        $linesPerSession = $student->group->type === 'two_lines' ? 2 : 7;
        $nextLinesTo = $nextLinesFrom + $linesPerSession - 1;

        if ($lastMemoProgress) {
            $lastPage = $lastMemoProgress->page;
            $nextPageNumber = $lastPage->number;
            $nextLinesFrom = $lastMemoProgress->lines_to + 1;
            $nextLinesTo = $nextLinesFrom + $linesPerSession - 1;

            if ($nextLinesTo > $lastPage->lines_count) {
                $nextPageNumber += 1;
                $nextLinesFrom = 1;
                $nextLinesTo = $nextLinesFrom + $linesPerSession - 1;
            }
        }

        $page = Page::where('number', $nextPageNumber)->first();
        $maxPage = Page::max('number');
        while (!$page && $nextPageNumber <= $maxPage) {
            $nextPageNumber += 1;
            $page = Page::where('number', $nextPageNumber)->first();
        }
        return [
            'page_id' => $page->id ?? null,
            'lines_from' => $nextLinesFrom - 1,
            'lines_to' => min($nextLinesTo, $page->lines_count - 1),
        ];
    }
}
