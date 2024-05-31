<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'phone', 'group', 'sex', 'city', 'group_id',
    ];

    protected $with = ['progresses', 'group', 'progresses.page'];

    public function progresses(): HasMany
    {
        return $this->hasMany(Progress::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function getProgressAttribute(): int
    {
        $page = $this->progresses->last()->prog ?? 0;
        $progress = $page * 100 / 604;

        return $progress;
    }

    public function needsCall(): bool
    {
        $threeDaysAgo = Carbon::now()->subDays(3);

        $recentProgresses = $this->progresses()->where('date', '>=', $threeDaysAgo)->get();

        $absentCount = $recentProgresses->where('status', 'absent')->count();

        return $absentCount >= 3;
    }

    public function getAbsenceAttribute(): int
    {
        return $this->progresses()->where('status', 'absent')->count();
    }
}
