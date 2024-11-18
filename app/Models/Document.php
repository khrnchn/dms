<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'title',
        'file_path',
        'file_type',
        'file_size',
        'department_id',
        'uploaded_by',
        'status', // enum: pending, approved, rejected
        'version',
        'description',
        'is_archived',
        'archived_at'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->department_id) {
                $model->department_id = auth()->user()->department_id;
            }
        });
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function accessLogs()
    {
        return $this->hasMany(DocumentAccessLog::class);
    }

    public function verifications()
    {
        return $this->hasMany(DocumentVerification::class);
    }
}
