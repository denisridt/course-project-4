<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserCreateRequest extends ApiRequest
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
            'name'      => 'required|string|min:1|max:64',
            'surname'   => 'required|string|min:1|max:64',
            'login'     => 'required|string|min:3|max:32|unique:users,login',
            'password'  => 'required|string|min:5|max:32',
            'email'     => 'required|email|unique:users,email',
            'telephone' => 'required|string|min:1|max:15|unique:users,telephone',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Поле "Имя" обязательно для заполнения.',
            'name.max'           => 'Поле "Имя" должно содержать не более :max символов.',

            'surname.required'   => 'Поле "Фамилия" обязательно для заполнения.',
            'surname.max'        => 'Поле "Фамилия" должно содержать не более :max символов.',

            'login.required'     => 'Поле "Логин" обязательно для заполнения.',
            'login.min'          => 'Поле "Логин" должно содержать не менее :min символов.',
            'login.max'          => 'Поле "Логин" должно содержать не более :max символов.',
            'login.unique'       => 'Такой "Логин" уже существует.',

            'password.required'  => 'Поле "Пароль" обязательно для заполнения.',
            'password.min'       => 'Поле "Пароль" должно содержать не менее :min символов.',
            'password.max'       => 'Поле "Пароль" должно содержать не более :max символов.',

            'email.required'     => 'Поле "Электронная почта" обязательно для заполнения.',
            'email.email'        => 'Поле "Электронная почта" должно быть действительным адресом электронной почты.',
            'email.unique'       => 'Такая "Электронная почта" уже существует.',

            'telephone.required' => 'Поле "Телефон" обязательно для заполнения.',
            'telephone.min'      => 'Поле "Телефон" должно содержать не менее :min символов.',
            'telephone.max'      => 'Поле "Телефон" должно содержать не более :max символов.',
            'telephone.unique'   => 'Такой "Телефон" уже существует.',
        ];
    }

}
