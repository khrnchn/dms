<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentVerification extends Model
{
    protected $fillable = [
        'document_id',
        'verified_by',
        'status', // enum: pending, verified, rejected
        'comments',
        'verified_at'
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
