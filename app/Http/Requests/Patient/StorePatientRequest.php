<?php

namespace App\Http\Requests\Patient;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientRequest extends FormRequest
{
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
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date', 'before_or_equal:today'],
            'gender' => ['required', 'in:male,female'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Имя обязательно.',
            'last_name.required' => 'Фамилия обязательна.',
            'birth_date.required' => 'Дата рождения обязательна.',
            'gender.required' => 'Пол обязателен.',
            'gender.in' => 'Пол может быть только: мужчина, женщина',
            'birth_date.before_or_equal' => 'Дата рождения не может быть в будущем'
        ];
    }
}
