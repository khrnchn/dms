<?php

namespace App\Filament\Resources;

use App\Enums\Role;
use App\Filament\Resources\DepartmentResource\Pages;
use App\Filament\Resources\DepartmentResource\RelationManagers\DocumentsRelationManager;
use App\Filament\Resources\DepartmentResource\RelationManagers\UsersRelationManager;
use App\Models\Department;
use App\Models\User;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Settings';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();

        if ($user->role === Role::MANAGER || $user->role === Role::SYSTEM_ADMIN) {
            return true;
        }

        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        // if not sysadmin, show only the user's department
        if ($user->role !== Role::SYSTEM_ADMIN) {
            return parent::getEloquentQuery()
                ->where('id', $user->department_id);
        }

        // show all
        return parent::getEloquentQuery();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->columnSpan(1)
                            ->placeholder('Enter department name'),

                        Select::make('file_admin_id')
                            ->label('File Admin')
                            ->columnSpan(1)
                            ->options(User::all()->pluck('name', 'id'))
                            ->placeholder('Select file admin'),

                        Textarea::make('description')
                            ->columnSpanFull()
                            ->rows(4)
                            ->placeholder('Enter department description'),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->wrap()
                    ->description(fn(Department $record): string => $record->description),
                TextColumn::make('fileAdmin.name')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
                            ->label('Department Name')
                            ->icon('heroicon-m-building-office'),
                        // ->weight('bold'),

                        TextEntry::make('created_at')
                            ->label('Created')
                            ->dateTime()
                            ->icon('heroicon-m-calendar'),

                        TextEntry::make('description')
                            ->label('Description')
                            ->icon('heroicon-m-information-circle')
                            ->columnSpanFull(),

                        TextEntry::make('fileAdmin.name')
                            ->label('File Administrator')
                            ->icon('heroicon-m-user-circle')
                            ->badge()
                            ->color('success'),

                        TextEntry::make('users_count')
                            ->label('Total Members')
                            ->state(fn(Department $record): int => $record->users()->count())
                            ->icon('heroicon-m-users')
                            ->color('info'),

                        TextEntry::make('documents_count')
                            ->label('Total Documents')
                            ->state(fn(Department $record): int => $record->documents()->count())
                            ->icon('heroicon-m-document')
                            ->color('warning'),
                    ])
                    ->columns([
                        'default' => 2,
                        'sm' => 2,
                        'lg' => 3
                    ]),

                InfolistSection::make('Recent Activity')
                    ->schema([
                        TextEntry::make('latest_documents')
                            ->label('Latest Documents')
                            ->state(function (Department $record): string {
                                $latestDocs = $record->documents()
                                    ->latest()
                                    ->take(3)
                                    ->pluck('title')
                                    ->join(', ');

                                return $latestDocs ?: 'No documents yet';
                            })
                            ->icon('heroicon-m-document-text')
                            ->columnSpanFull(),

                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->since()
                            ->icon('heroicon-m-clock'),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            UsersRelationManager::class,
            DocumentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDepartments::route('/'),
            'create' => Pages\CreateDepartment::route('/create'),
            'edit' => Pages\EditDepartment::route('/{record}/edit'),
        ];
    }
}
