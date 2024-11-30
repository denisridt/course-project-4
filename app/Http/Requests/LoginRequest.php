<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends ApiRequest
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
            'telephone'   => 'required|string|min:1|max:11',
            'password' => 'required|string|min:5|max:32',
        ];
    }
    public function messages()
    {
        return [
            'telephone.required' => 'Поле "telephone" не может быть пустым.',
            'telephone.max' => 'Поле "telephone" не может содержать более :max символов.',
            'telephone.min' => 'Поле "telephone" должно содержать не менее :min символов.',

            'password.required' => 'Поле "password" не может быть пустым.',
            'password.max' => 'Поле "password" не может содержать более :max символов.',
            'password.min' => 'Поле "password" должен содержать не менее :min символов.',
        ];
    }
}
