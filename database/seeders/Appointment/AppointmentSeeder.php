<?php

namespace Database\Seeders\Appointment;

use App\Models\Appointment;
use App\Models\AppointmentStatus;
use App\Enums\AppointmentStatus as AppointmentStatusEnum;
use App\Models\Patient;
use Carbon\CarbonImmutable;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\QueryException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $days            = (int) env('APPOINTMENTS_SEED_DAYS', 5);
        $slotMinutes     = (int) env('APPOINTMENTS_SEED_SLOT_MIN', 30);
        $fillProbability = (float) env('APPOINTMENTS_SEED_PROB', 0.6);
        $maxAppointments = (int) env('APPOINTMENTS_MAX', 50); // ← максимум

        $doctors = [
            ['name' => 'Иванов П.П.',   'spec' => 'Терапевт'],
            ['name' => 'Сидорова А.А.', 'spec' => 'Кардиолог'],
            ['name' => 'Петров К.К.',   'spec' => 'ЛОР'],
            ['name' => 'Морозов Л.Н.',  'spec' => 'Невролог'],
        ];

        $patientIds = Patient::query()->pluck('id')->all();
        if (empty($patientIds)) {
            $this->command->warn('Нет пациентов — создайте PatientSeeder или добавьте пациентов.');
            return;
        }

        $scheduledId = AppointmentStatus::query()
            ->where('slug', AppointmentStatusEnum::SCHEDULED->value)
            ->value('id');

        $patientTimeSet = [];
        $created = 0;

        $startDay = CarbonImmutable::now()->startOfDay()->addDay();
        $this->command->getOutput()->writeln("<info>Seeding appointments (max {$maxAppointments})...</info>");

        DB::beginTransaction();
        try {
            for ($d = 0; $d < $days; $d++) {
                if ($created >= $maxAppointments) break;

                $day = $startDay->addDays($d);
                $slot = $day->setTime(9, 0, 0);
                $end  = $day->setTime(17, 0, 0);

                while ($slot < $end) {
                    if ($created >= $maxAppointments) break;

                    $slotStr = $slot->format('Y-m-d H:i:00');

                    foreach ($doctors as $doc) {
                        if ($created >= $maxAppointments) break;

                        if (mt_rand() / mt_getrandmax() > $fillProbability) {
                            continue;
                        }

                        $tries = 0; $patientId = null;
                        while ($tries < 5) {
                            $candidate = $patientIds[array_rand($patientIds)];
                            if (!isset($patientTimeSet[$candidate][$slotStr])) {
                                $patientId = $candidate;
                                break;
                            }
                            $tries++;
                        }
                        if (!$patientId) {
                            continue;
                        }

                        try {
                            Appointment::create([
                                'patient_id'            => $patientId,
                                'doctor_name'           => $doc['name'],
                                'specialization'        => $doc['spec'],
                                'date_time'             => $slotStr,
                                'appointment_status_id' => $scheduledId,
                            ]);

                            $patientTimeSet[$patientId][$slotStr] = true;
                            $created++;

                            if ($created >= $maxAppointments) {
                                break 3;
                            }
                        } catch (QueryException $e) {
                            if ((int) ($e->errorInfo[1] ?? 0) === 1062) {
                                continue;
                            }
                            throw $e;
                        }
                    }

                    $slot = $slot->addMinutes($slotMinutes);
                }
            }

            DB::commit();
            $this->command->info("Готово");
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
