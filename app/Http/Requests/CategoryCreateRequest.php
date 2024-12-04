<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryCreateRequest extends ApiRequest
{

    public function authorize()
    {
        // Только администратор может создавать категории
        return auth()->user() && auth()->user()->role->name === 'admin';
    }
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:1|max:255|unique:categories',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Поле "Name" не может быть пустым.',
            'name.max'      => 'Поле "Name" не может содержать более :max символов.',
            'name.min'      => 'Поле "Name" должно содержать не менее :min символов.',
            'name.unique'   => 'Категория с таким именем уже существует.',
        ];
    }
}
