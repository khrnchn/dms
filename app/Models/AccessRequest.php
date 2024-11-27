<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessRequest extends Model
{
    protected $fillable = [
        'user_id',
        'document_id',
        'status', // enum: pending, approved, rejected
        'requested_at',
        'approved_by',
        'approved_at',
        'reason',
        'expiry_date'
    ];

    public function scopeForCurrentUser($query)
    {
        $user = auth()->user();

        return $query->whereHas('document', function ($query) use ($user) {
            $query->where('department_id', $user->department_id);
        })
            ->orWhere('user_id', $user->id);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
