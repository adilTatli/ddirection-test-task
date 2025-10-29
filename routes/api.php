<?php

use App\Http\Controllers\Api\PatientController;
use Illuminate\Support\Facades\Route;

Route::apiResource('patients', PatientController::class);
