<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *   schema="Patient",
 *   type="object",
 *   required={"id","first_name","last_name","birth_date","gender"},
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="first_name", type="string", example="Ivan"),
 *   @OA\Property(property="last_name", type="string", example="Ivanov"),
 *   @OA\Property(property="full_name", type="string", example="Ivanov Ivan"),
 *   @OA\Property(property="birth_date", type="string", format="date", example="1990-05-10"),
 *   @OA\Property(property="gender", type="string", enum={"male","female"}, example="male"),
 *   @OA\Property(property="created_at", type="string", format="date-time"),
 *   @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *   schema="PatientResource",
 *   type="object",
 *   @OA\Property(property="data", ref="#/components/schemas/Patient")
 * )
 *
 * @OA\Schema(
 *   schema="PatientCollection",
 *   type="object",
 *   @OA\Property(
 *     property="data",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/Patient")
 *   ),
 *   @OA\Property(property="links", ref="#/components/schemas/PaginationLinks"),
 *   @OA\Property(property="meta",  ref="#/components/schemas/PaginationMeta")
 * )
 *
 * @OA\Schema(
 *   schema="PatientCreate",
 *   type="object",
 *   required={"first_name","last_name","birth_date","gender"},
 *   @OA\Property(property="first_name", type="string", maxLength=255, example="Ivan"),
 *   @OA\Property(property="last_name",  type="string", maxLength=255, example="Ivanov"),
 *   @OA\Property(property="birth_date", type="string", format="date", example="1990-05-10"),
 *   @OA\Property(property="gender",     type="string", enum={"male","female"}, example="male")
 * )
 *
 * @OA\Schema(
 *   schema="PatientUpdate",
 *   type="object",
 *   @OA\Property(property="first_name", type="string", maxLength=255, example="Ivan"),
 *   @OA\Property(property="last_name",  type="string", maxLength=255, example="Petrov"),
 *   @OA\Property(property="birth_date", type="string", format="date", example="1991-01-01"),
 *   @OA\Property(property="gender",     type="string", enum={"male","female"}, example="male")
 * )
 *
 * @OA\Schema(
 *   schema="PaginationLinks",
 *   type="object",
 *   @OA\Property(property="first", type="string", nullable=true),
 *   @OA\Property(property="last",  type="string", nullable=true),
 *   @OA\Property(property="prev",  type="string", nullable=true),
 *   @OA\Property(property="next",  type="string", nullable=true)
 * )
 *
 * @OA\Schema(
 *   schema="PaginationMeta",
 *   type="object",
 *   @OA\Property(property="current_page", type="integer", example=1),
 *   @OA\Property(property="from",         type="integer", nullable=true, example=1),
 *   @OA\Property(property="last_page",    type="integer", example=1),
 *   @OA\Property(property="path",         type="string", example="http://localhost/api/patients"),
 *   @OA\Property(property="per_page",     type="integer", example=15),
 *   @OA\Property(property="to",           type="integer", nullable=true, example=1),
 *   @OA\Property(property="total",        type="integer", example=1)
 * )
 *
 * @OA\Schema(
 *   schema="ErrorResponse",
 *   type="object",
 *   @OA\Property(property="message", type="string"),
 *   @OA\Property(property="code",    type="string")
 * )
 *
 * @OA\Schema(
 *   schema="NotFoundResponse",
 *   allOf={@OA\Schema(ref="#/components/schemas/ErrorResponse")},
 *   @OA\Property(property="errors", type="object",
 *     @OA\Property(property="ids", type="array", @OA\Items(type="string"))
 *   ),
 *   example={
 *     "message": "Patient not found",
 *     "code": "NOT_FOUND",
 *     "errors": {"ids": {"999"}}
 *   }
 * )
 *
 * @OA\Schema(
 *   schema="ValidationErrorResponse",
 *   type="object",
 *   @OA\Property(property="message", type="string", example="Validation failed"),
 *   @OA\Property(property="code",    type="string", example="VALIDATION_ERROR"),
 *   @OA\Property(property="errors",  type="object",
 *     additionalProperties=@OA\Schema(type="array", @OA\Items(type="string"))
 *   )
 * )
 */
final class PatientSchemas {}
