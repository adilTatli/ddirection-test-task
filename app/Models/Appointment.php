<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_id',
        'doctor_name',
        'specialization',
        'date_time',
        'appointment_status_id',
    ];

    protected $casts = [
        'date_time' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function status()
    {
        return $this->belongsTo(AppointmentStatus::class, 'appointment_status_id');
    }

    public function scopeDoctor(Builder $q, ?string $name): Builder
    {
        return $name ? $q->where('doctor_name', 'like', "%{$name}%") : $q;
    }

    public function scopeSpecialization(Builder $q, ?string $spec): Builder
    {
        return $spec ? $q->where('specialization', 'like', "%{$spec}%") : $q;
    }

    public function scopeOrderByDate(Builder $q, string $dir = 'asc'): Builder
    {
        return $q->orderBy('date_time', $dir === 'desc' ? 'desc' : 'asc');
    }

    public function scopeForPatient(Builder $q, int $patientId): Builder
    {
        return $q->where('patient_id', $patientId);
    }
}
