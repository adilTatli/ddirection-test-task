<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patient\StorePatientRequest;
use App\Http\Requests\Patient\UpdatePatientRequest;
use App\Http\Resources\Patient\PatientResource;
use App\Models\Patient;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *   name="Patients",
 *   description="CRUD для пациентов"
 * )
 */
class PatientController extends Controller
{
    /**
     * Список пациентов (пагинация и поиск)
     *
     * @OA\Get(
     *   path="/api/patients",
     *   operationId="patientsIndex",
     *   tags={"Patients"},
     *   summary="Получить список пациентов",
     *   @OA\Parameter(
     *     name="search",
     *     in="query",
     *     description="Поиск по имени/фамилии (LIKE)",
     *     required=false,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Parameter(
     *     name="per_page",
     *     in="query",
     *     description="Размер страницы (по умолчанию 15)",
     *     required=false,
     *     @OA\Schema(type="integer", minimum=1, maximum=100)
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Успешно",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="data",
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/Patient")
     *       ),
     *       @OA\Property(property="links", type="object"),
     *       @OA\Property(property="meta", type="object")
     *     )
     *   )
     * )
     */
    public function index(Request $request)
    {
        $perPage = (int) ($request->integer('per_page') ?: 15);
        $search = trim((string) $request->query('search'));

        $patients = Patient::query()
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                   $q->where('first_name', 'like', '%' . $search . '%')
                       ->orWhere('last_name', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('first_name')
            ->orderBy('last_name');

        $paginator = $patients->paginate($perPage)
            ->appends($request->only('search', 'per_page'));

        return PatientResource::collection($paginator);
    }

    /**
     * Создать пациента
     *
     * @OA\Post(
     *   path="/api/patients",
     *   operationId="patientsStore",
     *   tags={"Patients"},
     *   summary="Создать пациента",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/PatientCreate")
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Создано",
     *     @OA\JsonContent(ref="#/components/schemas/Patient")
     *   ),
     *   @OA\Response(response=422, description="Валидация")
     * )
     */
    public function store(StorePatientRequest $request)
    {
        $patient = Patient::create($request->validated());

        return (new PatientResource($patient))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Показать пациента
     *
     * @OA\Get(
     *   path="/api/patients/{id}",
     *   operationId="patientsShow",
     *   tags={"Patients"},
     *   summary="Показать пациента",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/Patient")),
     *   @OA\Response(response=404, description="Не найден")
     * )
     */
    public function show(Patient $patient)
    {
        return (new PatientResource($patient));
    }

    /**
     * Обновить пациента
     *
     * @OA\Put(
     *   path="/api/patients/{id}",
     *   operationId="patientsUpdate",
     *   tags={"Patients"},
     *   summary="Обновить пациента",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/PatientUpdate")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/Patient")),
     *   @OA\Response(response=404, description="Не найден"),
     *   @OA\Response(response=422, description="Валидация")
     * )
     */
    public function update(UpdatePatientRequest $request, Patient $patient)
    {
        $patient->update($request->validated());
        return new PatientResource($patient->fresh());
    }

    /**
     * Удалить пациента
     *
     * @OA\Delete(
     *   path="/api/patients/{id}",
     *   operationId="patientsDestroy",
     *   tags={"Patients"},
     *   summary="Удалить пациента",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=204, description="Удалено"),
     *   @OA\Response(response=404, description="Не найден")
     * )
     */
    public function destroy(Patient $patient)
    {
        $patient->delete();
        return response()->noContent();
    }
}
