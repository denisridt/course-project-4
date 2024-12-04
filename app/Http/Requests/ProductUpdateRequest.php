<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends ApiRequest
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
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|integer|min:0',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'nullable|integer|exists:categories,id',
        ];
    }

    public function messages()
    {
        return [
            'name.max'            => 'Поле "Name" должно содержать не более :max символов.',
            'price.numeric'       => 'Поле "Price" должно быть числом.',
            'price.min'           => 'Поле "Price" должно быть не менее :min.',
            'quantity.integer'    => 'Поле "Quantity" должно быть целым числом.',
            'quantity.min'        => 'Поле "Quantity" должно быть не менее :min.',
            'photo.file'          => 'Поле "Photo" должно быть файлом.',
            'photo.mimes'         => 'Поле "Photo" должно быть файлом типа: jpeg, jpg, png, webp.',
            'photo.max'           => 'Файл в поле "Photo" должен быть не больше :max килобайт.',
            'category_id.integer' => 'Поле "Category ID" должно быть целым числом.',
            'category_id.exists'  => 'Категория с таким ID не существует.',
        ];
    }
}
