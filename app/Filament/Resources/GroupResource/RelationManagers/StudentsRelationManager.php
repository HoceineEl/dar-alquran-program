<?php

namespace App\Filament\Resources\GroupResource\RelationManagers;

use App\Helpers\ProgressFormHelper;
use App\Models\Progress;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
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
                Forms\Components\TextInput::make('city')
                    ->label('المدينة')
                    ->required(),
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
                    ->icon(fn ($record) => $record->needsCall() ? 'heroicon-o-exclamation-circle' : 'heroicon-o-check-circle')
                    ->color(fn (Student $record) => $record->needsCall() ? 'danger' : 'success')
                    ->label('رقم الهاتف'),
                TextColumn::make('sex')->label('الجنس')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'male' => 'ذكر',
                            'female' => 'أنثى',
                        };
                    }),
                TextColumn::make('city')->label('المدينة'),
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
                Tables\Actions\Action::make('send_whatsapp_msg')
                    ->color('success')
                    ->iconButton()
                    ->icon('heroicon-o-chat-bubble-oval-left')
                    ->label('إرسال رسالة واتساب')
                    ->url(fn ($record) => "https://wa.me/{$record->phone}?text=" . urlencode('السلام عليكم'), true),
                Tables\Actions\Action::make('progress')
                    ->icon('heroicon-o-chart-pie')
                    ->color('success')
                    ->modal()
                    ->disabled(fn ($record) => Progress::where('student_id', $record->id)->whereDate('date', now()->format('Y-m-d'))->exists())
                    ->color(fn ($record) => Progress::where('student_id', $record->id)->whereDate('date', now()->format('Y-m-d'))->exists() ? 'gray' : 'success')
                    ->label(fn ($record) => Progress::where('student_id', $record->id)->whereDate('date', now()->format('Y-m-d'))->exists() ? 'تم إضافة التقدم' : 'إضافة التقدم')
                    ->slideOver()
                    ->form(function (Model $student) {
                        return ProgressFormHelper::getProgressFormSchema($student);
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
}
