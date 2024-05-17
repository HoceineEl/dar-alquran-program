<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Progress extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'date', 'status', 'page', 'lines_from', 'lines_to'
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
