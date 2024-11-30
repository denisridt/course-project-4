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
            'name'=>'required|string|min:1|max:255|unique:categories'
        ];
    }
    public function messages()
    {
        return [
            'name.max' => 'Поле "Name" не может содержать более :max символов.',
            'name.min' => 'Поле "Name" должно содержать не менее :min символов.',
            'name.unique' => 'Такой "name" уже существует.',
        ];
    }
}
