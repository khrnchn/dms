<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentAccessLog extends Model
{
    protected $fillable = [
        'document_id',
        'user_id',
        'action', // enum: view, download, share
        'ip_address',
        'accessed_at'
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
