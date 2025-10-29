<?php

use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\PatientController;
use Illuminate\Support\Facades\Route;

//Роут пациентов
Route::apiResource('patients', PatientController::class);
//Poут записи пациентов
Route::apiResource('appointments', AppointmentController::class);
Route::patch('appointments/{appointment}/complete', [AppointmentController::class, 'complete']);
Route::get('/patients/{patient}/appointments', [AppointmentController::class, 'forPatient']);
