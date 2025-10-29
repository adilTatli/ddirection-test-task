<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *   schema="Appointment",
 *   type="object",
 *   required={"id","patient_id","doctor_name","specialization","date_time"},
 *   @OA\Property(property="id", type="integer", example=10),
 *   @OA\Property(property="patient_id", type="integer", example=1, description="ID пациента"),
 *   @OA\Property(property="doctor_name", type="string", example="Иванов С. П."),
 *   @OA\Property(property="specialization", type="string", example="Терапевт"),
 *   @OA\Property(property="date_time", type="string", format="date-time", example="2025-11-01T14:30:00+05:00"),
 *   @OA\Property(property="appointment_status_id", type="integer", nullable=true, example=1),
 *   @OA\Property(property="created_at", type="string", format="date-time"),
 *   @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *   schema="AppointmentResource",
 *   type="object",
 *   @OA\Property(property="data", ref="#/components/schemas/Appointment")
 * )
 *
 * @OA\Schema(
 *   schema="AppointmentCollection",
 *   type="object",
 *   @OA\Property(
 *     property="data",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/Appointment")
 *   ),
 *   @OA\Property(property="links", ref="#/components/schemas/PaginationLinks"),
 *   @OA\Property(property="meta",  ref="#/components/schemas/PaginationMeta")
 * )
 *
 * @OA\Schema(
 *   schema="AppointmentCreate",
 *   type="object",
 *   required={"patient_id","doctor_name","specialization","date_time"},
 *   @OA\Property(property="patient_id", type="integer", example=1),
 *   @OA\Property(property="doctor_name", type="string", maxLength=255, example="Иванов С. П."),
 *   @OA\Property(property="specialization", type="string", maxLength=255, example="Терапевт"),
 *   @OA\Property(property="date_time", type="string", format="date-time", example="2025-11-01 14:30:00")
 * )
 *
 * @OA\Schema(
 *   schema="AppointmentUpdate",
 *   type="object",
 *   @OA\Property(property="patient_id", type="integer", example=1),
 *   @OA\Property(property="doctor_name", type="string", maxLength=255, example="Иванов С. П."),
 *   @OA\Property(property="specialization", type="string", maxLength=255, example="Терапевт"),
 *   @OA\Property(property="date_time", type="string", format="date-time", example="2025-11-02 10:00:00")
 * )
 */
final class AppointmentSchemas {}
