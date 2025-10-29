<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('doctor_name', 255);
            $table->string('specialization', 255);
            $table->dateTime('date_time');
            $table->foreignId('appointment_status_id')->constrained('appointment_statuses')->cascadeOnUpdate()->restrictOnDelete();
            $table->unique(['patient_id', 'date_time']);
            $table->softDeletes();
            $table->tinyInteger('is_active')->storedAs('CASE WHEN `deleted_at` IS NULL THEN 1 ELSE 0 END')->index();
            $table->unique(['doctor_name','date_time','is_active'],'appointments_doctor_dt_active_unique');
            $table->index('date_time');
            $table->index('doctor_name');
            $table->index('specialization');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
