<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ayah extends Model
{
    use HasFactory;

    protected $fillable = [
        'surah_name',
        'page_number',
        'line_start',
        'line_end',
        'ayah_text',
    ];

    public function progress(): HasMany
    {
        return $this->hasMany(Progress::class);
    }

    public function getAyahNameAttribute(): string
    {
        return 'الصفحة '.$this->page_number.', '.$this->surah_name;
    }

    public function getAyaTextAttribute(): string
    {
        return $this->aya_text.' ('.$this->line_start.' - '.$this->line_end.') ,'.$this->surah_name.' ('.$this->page_number.')';
    }
}
