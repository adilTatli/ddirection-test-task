<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected const GENDERS = ['male', 'female'];

    protected $fillable = [
        'first_name',
        'last_name',
        'gender',
        'birth_date',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function getFullNameAttribute(): string
    {
        return trim("{$this->last_name} {$this->first_name}");
    }
}
