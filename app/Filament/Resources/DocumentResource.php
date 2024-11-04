<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentResource\Pages;
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
use Filament\Forms\Components\Section;
use Illuminate\Support\Facades\Storage;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter document title'),

                        Select::make('department_id')
                            ->relationship('department', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        FileUpload::make('file_path')
                            ->required()
                            ->disk('public')
                            ->directory('documents')
                            ->preserveFilenames()
                            ->maxSize(50 * 1024) // 50MB
                            ->acceptedFileTypes([
                                'application/pdf',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            ])
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    // Get file info
                                    $path = Storage::disk('public')->path($state);
                                    $fileInfo = pathinfo($path);

                                    // Set file type and size
                                    $set('file_type', $fileInfo['extension']);
                                    $set('file_size', filesize($path));
                                }
                            }),

                        TextInput::make('version')
                            ->default('1.0')
                            ->required()
                            ->maxLength(10)
                            ->placeholder('1.0'),

                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->default('pending')
                            ->required(),

                        Textarea::make('description')
                            ->maxLength(1000)
                            ->rows(3)
                            ->placeholder('Enter document description'),

                        // KeyValue::make('metadata')
                        // ->keyLabel('Property')
                        // ->valueLabel('Value')
                        // ->addButtonLabel('Add Property')
                        // ->reorderable(),

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
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
