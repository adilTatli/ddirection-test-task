<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patient\StorePatientRequest;
use App\Http\Requests\Patient\UpdatePatientRequest;
use App\Http\Resources\Patient\PatientResource;
use App\Models\Patient;
use HttpResponse;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
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
            ->orderBy('last_name')
            ->paginate($perPage);

        return $patients;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePatientRequest $request)
    {
        $patient = Patient::create($request->validated());

        return (new PatientResource($patient))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {
        return (new PatientResource($patient));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePatientRequest $request, Patient $patient)
    {
        $patient->update($request->validated());
        return new PatientResource($patient->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        $patient->delete();
        return response()->noContent();
    }
}
