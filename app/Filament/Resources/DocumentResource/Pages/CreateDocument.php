<?php

namespace App\Filament\Resources\DocumentResource\Pages;

use App\Filament\Resources\DocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Add uploaded_by field with current user's ID
        $data['uploaded_by'] = Auth::id();

        // If file metadata isn't set but we have a file, try to set it here
        if (empty($data['file_type']) && !empty($data['file_path'])) {
            $fileInfo = pathinfo($data['file_path']);
            $data['file_type'] = $fileInfo['extension'] ?? null;

            // Get file size if file exists
            if (Storage::disk('public')->exists($data['file_path'])) {
                $data['file_size'] = Storage::disk('public')->size($data['file_path']);
            }
        }

        return $data;
    }

    // Optional: Customize the redirect after successful creation
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
