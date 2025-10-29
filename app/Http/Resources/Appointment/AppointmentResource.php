<?php

namespace App\Http\Resources\Appointment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'patient'     => [
                'id'        => $this->patient_id,
                'first_name'=> $this->patient->first_name ?? null,
                'last_name' => $this->patient->last_name ?? null,
                'full_name' => method_exists($this->patient,'getFullNameAttribute')
                    ? $this->patient->full_name
                    : trim(($this->patient->last_name ?? '') . ' ' . ($this->patient->first_name ?? '')),
            ],
            'doctor_name'    => $this->doctor_name,
            'specialization' => $this->specialization,
            'date_time'      => optional($this->date_time)->toIso8601String(),
            'status'         => $this->relationLoaded('status') || $this->appointment_status_id
                ? [
                    'id'    => $this->status->id ?? $this->appointment_status_id,
                    'slug'  => $this->status->slug ?? null,
                    'title' => $this->status->title ?? null,
                ]
                : null,
        ];
    }
}
