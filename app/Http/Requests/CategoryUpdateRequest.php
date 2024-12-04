<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryUpdateRequest extends ApiRequest
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
            'name' => 'required|string|min:1|max:255|unique:categories,name,' . $this->route('id') // исключаем проверку уникальности для текущего ID
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Поле "Name" обязательно для заполнения.',
            'name.string' => 'Поле "Name" должно быть строкой.',
            'name.max' => 'Поле "Name" не может содержать более :max символов.',
            'name.min' => 'Поле "Name" должно содержать минимум :min символов.',
            'name.unique' => 'Категория с таким названием уже существует. Пожалуйста, выберите другое.',
        ];
    }
}
