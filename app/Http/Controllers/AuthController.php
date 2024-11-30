<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserCreateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        // Валидация запроса
        $credentials = $request->only('telephone', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Генерация токена
            $token = Str::random(60);

            // Сохранение токена в базе данных
            $user->api_token = $token;
            $user->save();

            return response()->json([
                'message' => 'Успешный вход',
                'token' => $token,
            ]);
        }

        return response()->json(['error' => 'Неверные данные'], 401);
    }

    public function logout(Request $request) {
        $user = $request->user();
        if (!$user) throw new ApiException(401, 'Ошибка аутентификации');
        $user->api_token = null;
        $user->save();
        return response([
            'data' => [
                'message' => 'Вы вышли из системы',
            ],
        ]);
    }

    public function register(UserCreateRequest $request)
    {
        // Создаем нового пользователя с предоставленными данными
        $user = new User($request->all());
        $user->save();
        return response([
            'message' => 'Регистрация прошла успешно'
        ], 201);
    }
}
