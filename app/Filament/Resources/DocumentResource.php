<?php

namespace App\Filament\Resources;

use App\Enums\Role;
use App\Filament\Resources\DocumentResource\Pages;
use App\Models\AccessRequest;
use App\Models\Document;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Database\Eloquent\Collection;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Hidden::make('file_type'),
                        Hidden::make('file_size'),
                        Hidden::make('file_size_formatted'),

                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2)
                            ->placeholder('Enter document title'),

                        TextInput::make('version')
                            ->default('1.0')
                            ->required()
                            ->maxLength(10)
                            ->placeholder('1.0'),

                        Textarea::make('description')
                            ->maxLength(1000)
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Enter document description'),

                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->disabled(fn() => !auth()->user()->role->isManager())
                            ->native(false)
                            ->default('pending'),

                        FileUpload::make('file_path')
                            ->required()
                            ->columnSpanFull()
                            ->disk('public')
                            ->directory('documents')
                            ->preserveFilenames()
                            ->maxSize(50 * 1024) // 50MB
                            ->visibility('public')
                            ->helperText('Max file size: 50MB. Allowed types: PDF, Word, Excel')
                            ->downloadable()
                            ->openable()
                            ->acceptedFileTypes([
                                'application/pdf',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            ])
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    // Handle both single file and array of files
                                    $file = is_array($state) ? $state[0] : $state;

                                    // Check if it's a temporary uploaded file
                                    if ($file instanceof TemporaryUploadedFile) {
                                        $set('file_type', $file->getClientOriginalExtension());
                                        $set('file_size', $file->getSize());

                                        // Optional: Format file size for display
                                        $formattedSize = number_format($file->getSize() / 1024 / 1024, 2) . ' MB';
                                        $set('file_size_formatted', $formattedSize);
                                    }
                                    // Handle already stored files
                                    elseif (is_string($file) && Storage::disk('public')->exists($file)) {
                                        $fileInfo = pathinfo($file);
                                        $set('file_type', $fileInfo['extension'] ?? '');
                                        $set('file_size', Storage::disk('public')->size($file));

                                        // Optional: Format file size for display
                                        $formattedSize = number_format(Storage::disk('public')->size($file) / 1024 / 1024, 2) . ' MB';
                                        $set('file_size_formatted', $formattedSize);
                                    }
                                } else {
                                    // Reset values if no file
                                    $set('file_type', null);
                                    $set('file_size', null);
                                    $set('file_size_formatted', null);
                                }
                            }),
                        // KeyValue::make('metadata')
                        //     ->keyLabel('Property')
                        //     ->valueLabel('Value')
                        //     ->addButtonLabel('Add Property')
                        //     ->reorderable(),

                        Toggle::make('is_archived')
                            ->default(false)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $set('archived_at', now());
                                } else {
                                    $set('archived_at', null);
                                }
                            }),

                        DateTimePicker::make('archived_at')
                            ->hidden(fn($get) => !$get('is_archived'))
                            ->disabled()
                            ->displayFormat('d/m/Y H:i'),


                    ])
                    ->columns(3)
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(20),

                TextColumn::make('version')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => ucfirst($state))
                    ->colors([
                        'danger' => 'rejected',
                        'warning' => 'pending',
                        'success' => 'approved',
                    ])
                    ->icons([
                        'heroicon-o-x-circle' => 'rejected',
                        'heroicon-o-clock' => 'pending',
                        'heroicon-o-check-circle' => 'approved',
                    ])
                    ->sortable(),

                TextColumn::make('department.name')
                    ->formatStateUsing(fn(string $state): string => ucfirst($state))
                    ->toggleable()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('file_type')
                    ->toggleable()
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => strtoupper($state))
                    ->colors(['primary']),

                TextColumn::make('file_size')
                    ->toggleable()
                    ->formatStateUsing(
                        fn($state) =>
                        number_format($state / 1024 / 1024, 2) . ' MB'
                    ),

                TextColumn::make('uploader.name')
                    ->label('Uploaded by')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Upload date')
                    ->dateTime('d F Y')
                    ->sortable(),

                TextColumn::make('accessRequests.expiry_date')
                    ->label('Access Expiry')
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
                    ->toggleable()
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->indicator('Status'),

                SelectFilter::make('department')
                    ->relationship('department', 'name')
                    ->preload()
                    ->searchable()
                    ->indicator('Department'),

                SelectFilter::make('file_type')
                    ->options([
                        'pdf' => 'PDF',
                        'doc' => 'DOC',
                        'docx' => 'DOCX',
                        'xls' => 'XLS',
                        'xlsx' => 'XLSX',
                    ])
                    ->indicator('File Type'),

                // TernaryFilter::make('is_archived')
                //     ->label('Archived Status')
                //     ->indicator('Archive Status'),

                // DateConstraint::make('created_at')
                //     ->label('Upload Date Range'),

                // Filter::make('large_files')
                //     ->label('Large Files (>10MB)')
                //     ->query(
                //         fn(Builder $query): Builder =>
                //         $query->where('file_size', '>', 10 * 1024 * 1024)
                //     )
                //     ->toggle(),
            ])
            ->actions([
                Action::make('request_access')
                    ->icon('heroicon-o-hand-raised')
                    ->label('Request Access')
                    ->color('warning')
                    ->visible(
                        fn($record) =>
                        $record->status === 'approved' &&
                            $record->department_id !== auth()->user()->department_id &&
                            !AccessRequest::where('document_id', $record->id)
                                ->where('user_id', auth()->id())
                                ->whereIn('status', ['pending', 'approved'])
                                ->exists()

                        // add additional condition to check if user is manager
                    )
                    ->action(function ($record) {
                        AccessRequest::create([
                            'document_id' => $record->id,
                            'user_id' => auth()->id(),
                            'status' => 'pending',
                            'requested_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Access Request Submitted')
                            ->body('Your request for document access has been sent.')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\EditAction::make()
                    ->visible(
                        fn($record) =>
                        $record->department_id === auth()->user()->department_id &&
                            $record->status !== 'pending'
                    ),

                Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->label('Download')
                    ->color('warning')
                    ->url(fn($record) => Storage::url($record->file_path))
                    ->openUrlInNewTab()
                    ->visible(
                        fn($record) => ($record->department_id === auth()->user()->department_id && $record->status !== 'pending') ||
                            AccessRequest::where('document_id', $record->id)
                            ->where('user_id', auth()->id())
                            ->where('status', 'approved')
                            ->where('expiry_date', '>', now())
                            ->exists() &&
                            Storage::disk('public')->exists($record->file_path)
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('archive')
                        ->label('Archive Selected')
                        // ->icon('heroicon-o-archive-box')
                        ->action(function (Collection $records): void {
                            $records->each(function ($record) {
                                $record->update([
                                    'is_archived' => true,
                                    'archived_at' => now(),
                                ]);
                            });

                            Notification::make()
                                ->title('Success')
                                ->success()
                                ->body('All selected records have been archived.')
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation(),

                    Tables\Actions\BulkAction::make('unarchive')
                        ->label('Unarchive Selected')
                        // ->icon('heroicon-o-archive-box-arrow-up')
                        ->action(function (Collection $records): void {
                            $records->each(function ($record) {
                                $record->update([
                                    'is_archived' => false,
                                    'archived_at' => null,
                                ]);
                            });

                            Notification::make()
                                ->title('Success')
                                ->success()
                                ->body('All selected records have been unarchived.')
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation(),
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
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
        ];
    }
}
