<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends ApiRequest
{
    /**
     * Определяет, авторизован ли пользователь для выполнения этого запроса.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Разрешить запрос
    }

    /**
     * Правила валидации для запроса.
     *
     * @return array
     */
    public function rules()
    {


        return [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'patronymic' => 'nullable|string|max:255',
            'login' => 'required|string|max:32' ,
            'email' => 'required|email',
            'telephone' => 'required|digits_between:10,15',
            'birthday' => 'nullable|date',
            'password' => 'nullable|string|min:8|confirmed',
        ];
    }

    /**
     * Кастомные сообщения валидации.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'Поле имя обязательно для заполнения.',
            'surname.required' => 'Поле фамилия обязательно для заполнения.',
            'login.unique' => 'Этот логин уже используется.',
            'email.unique' => 'Этот e-mail уже используется.',
            'password.confirmed' => 'Пароли не совпадают.',
        ];
    }
}
