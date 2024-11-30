<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'address' => 'required|string|min:10',


        ];
    }

    /**
     * Сообщения об ошибках для валидации.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'address.required' => 'Поле адреса обязательно для заполнения.',
            'address.string' => 'Адрес должен быть строкой.',
            'address.min' => 'Адрес должен быть не короче 10 символов.',
        ];
    }
}
