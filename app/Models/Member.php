<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'type', 'phone', 'group', 'sex'
    ];

    public function progress() : HasMany
    {
        return $this->hasMany(Progress::class);
    }
}