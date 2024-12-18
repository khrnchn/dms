<?php

namespace App\Filament\Resources;

use App\Enums\Role;
use App\Filament\Resources\AccessRequestResource\Pages;
use App\Models\AccessRequest;
use Filament\Forms\Components\DatePicker;
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
                        'pending_file_admin' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger'
                    })
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pending' => 'Pending',
                        'pending_file_admin' => 'Pending File Admin',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        default => ucfirst($state), // Fallback for any other states
                    }),
                TextColumn::make('created_at')->label('Requested At')->dateTime(),
                TextColumn::make('expiry_date')
                    ->label('Expiry Date')
                    ->dateTime('d F Y')
                    ->color('danger')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        if (empty($state)) {
                            return 'N/A';
                        }

                        $expiryDate = \Carbon\Carbon::parse($state);

                        if ($expiryDate->isPast()) {
                            return $expiryDate->format('d F Y') . ' (Expired)';
                        }

                        return $expiryDate->format('d F Y');
                    })
            ])
            ->actions([
                // approve
                Action::make('approve')
                    ->label('Manager Approve')
                    ->color('success')
                    ->modalHeading('Approve Access Request')
                    ->modalWidth('md')
                    ->form([
                        DatePicker::make('expiry_date')
                            ->label('Expiry Date')
                            ->required()
                            ->minDate(now())
                            ->placeholder('Select expiry date')
                            ->native(false)
                    ])
                    ->action(function (AccessRequest $record, array $data) {
                        $record->update([
                            'manager_approval_status' => 'approved',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                            'expiry_date' => $data['expiry_date'],
                            'status' => 'pending_file_admin' // new intermediate status
                        ]);

                        Notification::make()
                            ->title('Access Request Pending File Admin Approval')
                            ->body("Document access request requires file admin approval.")
                            ->success()
                            ->send();
                    })
                    ->visible(
                        fn($record) =>
                        $record->status === 'pending' &&
                            auth()->user()->role === Role::MANAGER
                    ),

                Action::make('file_admin_approve')
                    ->label('File Admin Approve')
                    ->color('success')
                    ->action(function (AccessRequest $record) {
                        $record->update([
                            'file_admin_approval_status' => 'approved',
                            'status' => 'approved' // final approved status
                        ]);

                        Notification::make()
                            ->title('Access Request Fully Approved')
                            ->body("Document access has been granted.")
                            ->success()
                            ->send();
                    })
                    ->visible(
                        fn($record) =>
                        $record->status === 'pending_file_admin' &&
                            auth()->user()->role === Role::FILE_ADMIN
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
