<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Progress extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'date', 'status', 'page_id', 'lines_from', 'lines_to', 'notes', 'created_by',
        'prog', 'comment',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($progress) {
            $progress->created_by = auth()->id();
        });
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
