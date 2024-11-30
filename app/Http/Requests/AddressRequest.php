<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends ApiRequest
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
    public function rules()
    {
        return [
            'city' => 'required|string|max:100',
            'street' => 'required|string|max:255',
            'house' => 'required|string|max:20',
            'floor' => 'nullable|string|max:10',
            'apartment' => 'nullable|string|max:20',
            'entrance' => 'nullable|string|max:10',
            'intercom' => 'nullable|string|max:50',
            'comment' => 'nullable|string|max:500',
        ];
    }


    public function messages()
    {
        return [
            'city.required' => 'Поле города обязательно для заполнения.',
            'city.string' => 'Город должен быть строкой.',
            'city.max' => 'Город не может быть длиннее 100 символов.',

            'street.required' => 'Поле улицы обязательно для заполнения.',
            'street.string' => 'Улица должна быть строкой.',
            'street.max' => 'Улица не может быть длиннее 255 символов.',

            'house.required' => 'Поле дома обязательно для заполнения.',
            'house.string' => 'Дом должен быть строкой.',
            'house.max' => 'Дом не может быть длиннее 20 символов.',

            'floor.string' => 'Этаж должен быть строкой.',
            'floor.max' => 'Этаж не может быть длиннее 10 символов.',

            'apartment.string' => 'Квартира должна быть строкой.',
            'apartment.max' => 'Квартира не может быть длиннее 20 символов.',

            'entrance.string' => 'Подъезд должен быть строкой.',
            'entrance.max' => 'Подъезд не может быть длиннее 10 символов.',

            'intercom.string' => 'Домофон должен быть строкой.',
            'intercom.max' => 'Домофон не может быть длиннее 50 символов.',

            'comment.string' => 'Комментарий должен быть строкой.',
            'comment.max' => 'Комментарий не может быть длиннее 500 символов.',
        ];
    }
}
