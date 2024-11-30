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
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'price' => 'numeric|min:0',
            'quantity' => 'integer|min:0',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'integer|exists:categories,id',
        ];
    }
    public function messages()
    {
        return [
            'name.max'            => 'Поле "Name" должно содержать не более :max символов.',
            'name.min'            => 'Поле "Name" должно содержать минимум :min символов.',

            'price.numeric'       => 'Поле "Price" должно быть числом.',
            'price.min'           => 'Поле "Price" должно быть не менее :min.',
            'price.regex'         => 'Недопустимый формат поля "Price".',

            'quantity.integer'    => 'Поле "Quantity" должно быть целым числом.',
            'quantity.min'        => 'Поле "Quantity" должно быть не менее :min.',

            'photo.file'          => 'Поле "Photo" должно быть файлом.',
            'photo.mimes'         => 'Поле "Photo" должно быть файлом типа: jpeg, jpg, png, webp.',
            'photo.max'           => 'Файл в поле "Photo" должен быть не больше :max килобайт.',

            'category_id.integer' => 'Поле "Categories ID" должно быть целым числом.',
        ];
    }
}
