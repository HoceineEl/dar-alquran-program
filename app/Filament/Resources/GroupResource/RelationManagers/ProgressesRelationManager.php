<?php

namespace App\Filament\Resources\GroupResource\RelationManagers;

use App\Helpers\ProgressFormHelper;
use App\Models\Page;
use App\Models\Progress;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ProgressesRelationManager extends RelationManager
{
    protected static string $relationship = 'progresses';

    protected static ?string $title = 'التقدم';

    protected static ?string $navigationLabel = 'التقدم';

    protected static ?string $modelLabel = 'تقدم';

    protected static ?string $pluralModelLabel = 'تقدمات';

    public function form(Form $form): Form
    {
        return $form
            ->schema(ProgressFormHelper::getProgressFormSchema());
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('date')
            ->columns([
                Tables\Columns\TextColumn::make('date'),
                TextColumn::make('student.name')
                    ->label('الطالب'),
                TextColumn::make('page.number')
                    ->label('الصفحة'),
                TextColumn::make('status')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'memorized' => 'تم الحفظ',
                            'absent' => 'غائب',
                        };
                    })
                    ->label('الحالة'),
                TextColumn::make('lines_from')
                    ->label('من'),
                TextColumn::make('lines_to')
                    ->label('إلى'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Action::make('group')
                    ->label('تسجيل تقدم جماعي')
                    ->color(Color::Teal)
                    ->form(function () {
                        $students = $this->ownerRecord->students->pluck('name', 'id');

                        return [
                            Grid::make()
                                ->schema([
                                    Select::make('students')
                                        ->label('الطلاب')
                                        ->options(fn () => $students)
                                        ->required()
                                        ->multiple(),
                                    DatePicker::make('date')
                                        ->label('التاريخ')
                                        ->default(now()->format('Y-m-d'))
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
                                            'call_made' => 'تم الاتصال',
                                        ]),
                                ]),
                            Grid::make()
                                ->columns(3)
                                ->hidden(fn (Get $get) => $get('status') !== 'memorized')
                                ->schema([
                                    Select::make('page_id')
                                        ->label('الصفحة')
                                        ->options(fn () => Page::all()->pluck('number', 'id'))
                                        ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->number} - {$record->surah_name} - {$record->lines_count} سطر")
                                        ->preload()
                                        ->reactive()
                                        ->optionsLimit(700)
                                        ->searchable()
                                        ->required(),
                                    Select::make('lines_from')
                                        ->label('من السطر')
                                        ->reactive()
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
                                        ->label('إلى السطر')
                                        ->required(),
                                ]),
                            MarkdownEditor::make('notes')
                                ->label('ملاحظات')
                                ->columnSpanFull()
                                ->placeholder('أدخل ملاحظاتك هنا'),
                        ];
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->filters([
                SelectFilter::make('date')
                    ->options(function () {
                        return Progress::all()->pluck('date', 'date');
                    })
                    ->default(now()->format('Y-m-d')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
