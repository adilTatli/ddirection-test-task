<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\StoreAppointmentRequest;
use App\Http\Requests\Appointment\UpdateAppointmentRequest;
use App\Http\Resources\Appointment\AppointmentResource;
use App\Models\Appointment;
use App\Models\AppointmentStatus;
use App\Enums\AppointmentStatus as AppointmentStatusEnum;
use App\Models\Patient;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *   name="Appointments",
 *   description="Записи на приём"
 * )
 */
class AppointmentController extends Controller
{
    /**
     * Список записей (фильтры/сортировка/пагинация)
     *
     * @OA\Get(
     *   path="/api/appointments",
     *   operationId="appointmentsIndex",
     *   tags={"Appointments"},
     *   summary="Получить список записей на приём",
     *   @OA\Parameter(name="doctor_name", in="query", required=false, @OA\Schema(type="string")),
     *   @OA\Parameter(name="specialization", in="query", required=false, @OA\Schema(type="string")),
     *   @OA\Parameter(name="sort", in="query", description="Сортировка по дате: asc|desc", required=false, @OA\Schema(type="string", enum={"asc","desc"})),
     *   @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", minimum=1, maximum=100)),
     *   @OA\Response(
     *     response=200,
     *     description="Успешно",
     *     @OA\JsonContent(ref="#/components/schemas/AppointmentCollection")
     *   )
     * )
     */
    public function index(Request $request)
    {
        $perPage = (int) ($request->integer('per_page') ?: 15);
        $doctor  = trim((string) $request->query('doctor_name', ''));
        $spec    = trim((string) $request->query('specialization', ''));
        $sort    = strtolower((string) $request->query('sort', 'asc')) === 'desc' ? 'desc' : 'asc';

        $q = Appointment::query()
            ->with(['patient','status'])
            ->when($doctor !== '', fn($qq) => $qq->where('doctor_name','like',"%{$doctor}%"))
            ->when($spec   !== '', fn($qq) => $qq->where('specialization','like',"%{$spec}%"))
            ->orderBy('date_time', $sort);

        $p = $q->paginate($perPage)->appends($request->only('doctor_name','specialization','sort','per_page'));

        return AppointmentResource::collection($p);
    }

    /**
     * Создать запись
     *
     * @OA\Post(
     *   path="/api/appointments",
     *   operationId="appointmentsStore",
     *   tags={"Appointments"},
     *   summary="Создать запись на приём",
     *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/AppointmentCreate")),
     *   @OA\Response(response=201, description="Создано", @OA\JsonContent(ref="#/components/schemas/Appointment")),
     *   @OA\Response(response=422, description="Валидация")
     * )
     */
    public function store(StoreAppointmentRequest $request)
    {
        $scheduled = AppointmentStatus::firstOrCreate(
            ['slug' => AppointmentStatusEnum::SCHEDULED->value],
            ['title' => AppointmentStatusEnum::SCHEDULED->label()]
        );

        if (!$scheduled?->id) {
            throw new HttpResponseException(
                response()->json(['message' => 'Не удалось определить статус по умолчанию как (назначен).'], 500)
            );
        }

        $data = $request->validated();
        unset($data['appointment_status_id']);
        $data['appointment_status_id'] = $scheduled->id;

        $appointment = Appointment::create($data);

        return (new AppointmentResource($appointment->load(['patient','status'])))
            ->response()->setStatusCode(201);
    }

    /**
     * Показать запись
     *
     * @OA\Get(
     *   path="/api/appointments/{id}",
     *   operationId="appointmentsShow",
     *   tags={"Appointments"},
     *   summary="Показать запись",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/Appointment")),
     *   @OA\Response(response=404, description="Не найдено")
     * )
     */
    public function show(Appointment $appointment)
    {
        return new AppointmentResource($appointment->load(['patient','status']));
    }

    /**
     * Обновить запись (перенос/редактирование)
     *
     * @OA\Put(
     *   path="/api/appointments/{id}",
     *   operationId="appointmentsUpdate",
     *   tags={"Appointments"},
     *   summary="Обновить запись",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/AppointmentUpdate")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/Appointment")),
     *   @OA\Response(response=404, description="Не найдено"),
     *   @OA\Response(response=422, description="Валидация")
     * )
     */
    public function update(UpdateAppointmentRequest $request, Appointment $appointment)
    {
        $appointment->update($request->validated());
        return new AppointmentResource($appointment->fresh()->load(['patient','status']));
    }

    /**
     * Удалить (отменить) запись — освобождает слот
     *
     * @OA\Delete(
     *   path="/api/appointments/{id}",
     *   operationId="appointmentsDestroy",
     *   tags={"Appointments"},
     *   summary="Отменить запись (soft delete)",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/Appointment")),
     *   @OA\Response(response=404, description="Не найдено")
     * )
     */
    public function destroy(Appointment $appointment)
    {
        $canceledId = \App\Models\AppointmentStatus::query()
            ->where('slug', \App\Enums\AppointmentStatus::CANCELED->value)
            ->value('id');

        if ($canceledId) {
            $appointment->appointment_status_id = $canceledId;
        }

        $appointment->save();
        $appointment->delete();

        $appointment = Appointment::withTrashed()->findOrFail($appointment->id);

        return new AppointmentResource(
            $appointment->load(['patient','status'])
        );
    }

    /**
     * Завершить запись (пометить как completed)
     *
     * @OA\Patch(
     *   path="/api/appointments/{id}/complete",
     *   operationId="appointmentsComplete",
     *   tags={"Appointments"},
     *   summary="Завершить запись (completed)",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/Appointment")),
     *   @OA\Response(response=404, description="Не найдено")
     * )
     */
    public function complete(Appointment $appointment)
    {
        $completedId = AppointmentStatus::query()
            ->where('slug', AppointmentStatusEnum::COMPLETED->value)
            ->value('id');

        if (!$completedId) {
            return response()->json([
                'message' => 'Статус completed не найден.',
                'code'    => 'STATUS_NOT_FOUND',
            ], 500);
        }

        $currentSlug = optional($appointment->status)->slug;
        $allowedFrom = ['scheduled', 'confirmed'];

        if (!in_array($currentSlug, $allowedFrom, true)) {
            return response()->json([
                'message' => 'Нельзя завершить приём из текущего статуса.',
                'code'    => 'INVALID_STATUS_TRANSITION',
            ], 409);
        }

        if ((int)$appointment->appointment_status_id === (int)$completedId) {
            return new AppointmentResource(
                $appointment->load(['patient','status'])
            );
        }

        $appointment->update(['appointment_status_id' => $completedId]);

        return new AppointmentResource(
            $appointment->fresh()->load(['patient','status'])
        );
    }

    /**
     * Записи конкретного пациента
     *
     * @OA\Get(
     *   path="/api/patients/{patient}/appointments",
     *   operationId="appointmentsForPatient",
     *   tags={"Appointments"},
     *   summary="Получить записи конкретного пациента",
     *   @OA\Parameter(name="patient", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Parameter(name="doctor_name", in="query", required=false, @OA\Schema(type="string")),
     *   @OA\Parameter(name="specialization", in="query", required=false, @OA\Schema(type="string")),
     *   @OA\Parameter(name="sort", in="query", required=false, @OA\Schema(type="string", enum={"asc","desc"})),
     *   @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", minimum=1, maximum=100)),
     *   @OA\Response(response=200, description="Успешно", @OA\JsonContent(ref="#/components/schemas/AppointmentCollection")),
     *   @OA\Response(response=404, description="Пациент не найден")
     * )
     */
    public function forPatient(Patient $patient, Request $request)
    {
        $perPage = (int) ($request->integer('per_page') ?: 15);
        $doctor  = trim((string) $request->query('doctor_name', ''));
        $spec    = trim((string) $request->query('specialization', ''));
        $sort    = strtolower((string) $request->query('sort', 'asc')) === 'desc' ? 'desc' : 'asc';

        $q = Appointment::query()
            ->with(['patient','status'])
            ->where('patient_id', $patient->id)
            ->when($doctor !== '', fn($qq) => $qq->where('doctor_name','like',"%{$doctor}%"))
            ->when($spec   !== '', fn($qq) => $qq->where('specialization','like',"%{$spec}%"))
            ->orderBy('date_time', $sort);

        $p = $q->paginate($perPage)->appends($request->only('doctor_name','specialization','sort','per_page'));

        return AppointmentResource::collection($p);
    }
}
