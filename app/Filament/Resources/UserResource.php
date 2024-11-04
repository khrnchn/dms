<?php

namespace App\Filament\Resources;

use App\Enums\Role;
use App\Filament\Exports\UserExporter;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\Section;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationGroup = 'Settings';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->columnSpan(2)
                            ->required()
                            ->placeholder('Enter name')
                            ->maxLength(70),

                        TextInput::make('email')
                            ->email()
                            ->placeholder('Enter email')
                            ->required(),

                        TextInput::make('password')
                            ->password()
                            ->placeholder('Enter password')
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn(string $context): bool => $context === 'create'),

                        Select::make('role')
                            ->options(Role::options())
                            ->placeholder('Select role')
                            ->default(Role::STAFF->value)
                            ->required()
                            ->preload()
                            ->searchable(),

                        Select::make('department_id')
                            ->relationship('department', 'name')
                            ->placeholder('Select department')
                            ->searchable()
                            ->preload()
                            ->required(fn(Get $get): bool => $get('role') !== Role::SYSTEM_ADMIN->value),
                    ])->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('role')
                    ->badge()
                    ->formatStateUsing(fn(Role $state): string => $state->label())
                    ->color(fn(Role $state): string => match ($state) {
                        Role::SYSTEM_ADMIN => 'gray',
                        Role::FILE_ADMIN => 'warning',
                        Role::MANAGER => 'success',
                        Role::STAFF => 'danger',
                    }),

                TextColumn::make('department.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->options(Role::class)
                    ->multiple()
                    ->preload()
                    ->label('Filter by Role')
                    ->indicator('Roles'),

                SelectFilter::make('department')
                    ->relationship('department', 'name')
                    ->preload()
                    ->searchable()
                    ->multiple()
                    ->label('Filter by Department')
                    ->indicator('Departments'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(UserExporter::class)
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    // to add it as a table bulk action, so that the user can choose which rows to export
                    // ExportBulkAction::make()
                    //     ->exporter(UserExporter::class)
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistSection::make()
                    ->schema([
                        TextEntry::make('name')
                            ->label('Full Name')
                            ->icon('heroicon-m-user'),

                        TextEntry::make('email')
                            ->label('Email Address')
                            ->icon('heroicon-m-envelope')
                            ->copyable(),

                        TextEntry::make('role')
                            ->badge()
                            ->formatStateUsing(fn(Role $state): string => $state->label())
                            ->color(fn(Role $state): string => match ($state) {
                                Role::SYSTEM_ADMIN => 'gray',
                                Role::FILE_ADMIN => 'warning',
                                Role::MANAGER => 'success',
                                Role::STAFF => 'danger',
                            }),

                        TextEntry::make('department.name')
                            ->label('Department')
                            ->icon('heroicon-m-building-office'),
                    ])
                    ->columns(2),

                InfolistSection::make('Additional Details')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Joined')
                            ->dateTime()
                            ->icon('heroicon-m-calendar'),

                        TextEntry::make('documents_count')
                            ->label('Documents Uploaded')
                            ->state(fn(User $record): int => $record->documents()->count())
                            ->icon('heroicon-m-document')
                            ->color('success'),
                    ])
                    ->columns(2),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
