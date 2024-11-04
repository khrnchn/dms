<?php

namespace App\Filament\Resources;

use App\Enums\Role;
use App\Filament\Exports\UserExporter;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
