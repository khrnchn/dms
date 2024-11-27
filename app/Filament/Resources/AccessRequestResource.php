<?php

namespace App\Filament\Resources;

use App\Enums\Role;
use App\Filament\Resources\AccessRequestResource\Pages;
use App\Models\AccessRequest;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;

class AccessRequestResource extends Resource
{
    protected static ?string $model = AccessRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->query(fn() => AccessRequest::forCurrentUser())
            ->columns([
                TextColumn::make('document.title')->label('Document'),
                TextColumn::make('user.name')->label('Requested By'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger'
                    }),
                TextColumn::make('created_at')->label('Requested At')->dateTime()
            ])
            ->actions([
                // approve
                Action::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->action(function (AccessRequest $record) {
                        $record->update([
                            'status' => 'approved',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                    
                        Notification::make()
                            ->title('Access Request Approved')
                            ->body('Document access has been granted.')
                            ->success()
                            ->send();
                    })
                    ->visible(
                        fn($record) =>
                        $record->status === 'pending' &&
                            auth()->user()->role === Role::MANAGER
                    ),

                // reject
                Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->action(function (AccessRequest $record) {
                        $record->update([
                            'status' => 'rejected',
                        ]);

                        Notification::make()
                            ->title('Access Request Rejected')
                            ->body('Document access request has been denied.')
                            ->danger()
                            ->send();
                    })
                    ->visible(
                        fn($record) =>
                        $record->status === 'pending' &&
                            $record->document->department_id === auth()->user()->department_id &&
                            auth()->user()->role === Role::MANAGER
                    ),
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
            'index' => Pages\ListAccessRequests::route('/'),
            'create' => Pages\CreateAccessRequest::route('/create'),
            'edit' => Pages\EditAccessRequest::route('/{record}/edit'),
        ];
    }
}
