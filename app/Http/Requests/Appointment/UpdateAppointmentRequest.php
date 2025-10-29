<?php

namespace App\Http\Requests\Appointment;

use App\Models\Appointment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class UpdateAppointmentRequest extends FormRequest
{
    private const SLOT_MINUTES = 30;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'patient_id'     => ['sometimes','integer','exists:patients,id'],
            'doctor_name'    => ['sometimes','string','max:255'],
            'specialization' => ['sometimes','string','max:255'],
            'date_time'      => ['sometimes','date_format:Y-m-d H:i:s','after_or_equal:now'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $appointment = $this->route('appointment');
            if (!$appointment) return;

            $patientId  = (int) ($this->input('patient_id', $appointment->patient_id));
            $doctorName = (string) ($this->input('doctor_name', $appointment->doctor_name));
            $dateStr    = (string) ($this->input('date_time', optional($appointment->date_time)->format('Y-m-d H:i:s')));

            $dt = Carbon::createFromFormat('Y-m-d H:i:s', $dateStr, config('app.timezone', 'UTC'));
            if ($dt->second !== 0 || ($dt->minute % self::SLOT_MINUTES) !== 0) {
                $v->errors()->add('date_time', 'Время должно быть кратно 30 минутам (например 09:00:00, 09:30:00).');
                return;
            }

            $slotStart = $dt->copy();
            $slotEnd   = $dt->copy()->addMinutes(self::SLOT_MINUTES);

            $patientConflict = Appointment::query()
                ->where('patient_id', $patientId)
                ->where('date_time', '>=', $slotStart)
                ->where('date_time', '<',  $slotEnd)
                ->where('id','!=',$appointment->id)
                ->exists();
            if ($patientConflict) {
                $v->errors()->add('date_time', 'У пациента уже есть запись в этот временной слот.');
            }

            $doctorConflict = Appointment::query()
                ->where('doctor_name', $doctorName)
                ->where('date_time', '>=', $slotStart)
                ->where('date_time', '<',  $slotEnd)
                ->where('id','!=',$appointment->id)
                ->exists();
            if ($doctorConflict) {
                $v->errors()->add('doctor_name', 'У этого врача уже занят этот 30-минутный слот.');
            }
        });
    }
}
