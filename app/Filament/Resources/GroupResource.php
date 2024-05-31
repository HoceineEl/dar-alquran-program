<?php

namespace App\Filament\Resources;

use App\Classes\Core;
use App\Filament\Resources\GroupResource\Pages;
use App\Filament\Resources\GroupResource\RelationManagers\ManagersRelationManager;
use App\Filament\Resources\GroupResource\RelationManagers\ProgressesRelationManager;
use App\Filament\Resources\GroupResource\RelationManagers\StudentsRelationManager;
use App\Models\Group;
use App\Models\Message;
use App\Models\Student;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action as ActionsAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GroupResource extends Resource
{
    protected static ?string $model = Group::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'المجموعات';

    protected static ?string $modelLabel = 'مجموعة';

    protected static ?string $pluralModelLabel = 'مجموعات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('الاسم')
                    ->required(),
                Forms\Components\ToggleButtons::make('type')
                    ->label('نوع الحفظ')
                    ->inline()
                    ->options([
                        'two_lines' => 'سطران',
                        'half_page' => 'نصف صفحة',
                    ])
                    ->default('two_lines'),
            ])
            ->disabled(!Core::canChange());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('نوع الحفظ')
                    ->formatStateUsing(
                        function ($state) {
                            return match ($state) {
                                'two_lines' => 'سطران',
                                'half_page' => 'نصف صفحة',
                            };
                        },
                    )
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('managers.name')
                    ->label('المشرفون')
                    ->badge()
                    ->searchable(),
                TextColumn::make('created_at')->label('تاريخ الإنشاء')
                    ->date('Y-m-d H:i:s'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('send_whatsapp_group')
                    ->label('أرسل عبر الواتساب للغائبين')
                    ->icon('heroicon-o-users')
                    ->color('info')
                    ->action(function (Group $record) {
                        Core::sendMessageToAbsence($record);
                    }),
            ])
            ->headerActions([
                ActionsAction::make('send_whatsapp')
                    ->label('أرسل عبر الواتساب للغائبين')
                    ->icon('heroicon-o-users')
                    ->action(function () {
                        Core::sendMessageToAbsence();
                    }),
                ActionsAction::make('send_to_specific')
                    ->color('info')
                    ->icon('heroicon-o-cube')
                    ->label('أرسل لطلبة محددين')
                    ->form([
                        Select::make('students')
                            ->label('الطلبة')
                            ->options(Student::pluck('name', 'id')->toArray())
                            ->multiple()
                            ->required(),
                        ToggleButtons::make('message_type')
                            ->label('نوع الرسالة')
                            ->options([
                                'message' => 'قالب رسالة',
                                'custom' => 'رسالة مخصصة',
                            ])
                            ->reactive()
                            ->default('message')
                            ->inline(),
                        Select::make('message')
                            ->label('الرسالة')
                            ->native()
                            ->hidden(fn (Get $get) => $get('message_type') === 'custom')
                            ->options(Message::pluck('name', 'id')->toArray())
                            ->hintActions([
                                FormAction::make('create')
                                    ->label('إنشاء قالب')
                                    ->slideOver()
                                    ->modalWidth('4xl')
                                    ->icon('heroicon-o-plus-circle')
                                    ->form([
                                        TextInput::make('name')
                                            ->label('اسم القالب')
                                            ->required(),
                                        Textarea::make('content')
                                            ->label('الرسالة')
                                            ->rows(10)
                                            ->required(),
                                    ])
                                    ->action(function (array $data) {
                                        Message::create($data);

                                        Notification::make()
                                            ->title('تم إنشاء قالب الرسالة')
                                            ->color('success')
                                            ->icon('heroicon-o-check-circle')
                                            ->send();
                                    }),
                            ])
                            ->required(),
                        Textarea::make('message')
                            ->label('الرسالة')
                            ->hidden(fn (Get $get) => $get('message_type') !== 'custom')
                            ->rows(10)
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        Core::sendMessageToSpecific($data);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->disabled(Core::canChange()),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            StudentsRelationManager::class,
            ProgressesRelationManager::class,
            ManagersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGroups::route('/'),
            'create' => Pages\CreateGroup::route('/create'),
            'edit' => Pages\EditGroup::route('/{record}/edit'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
