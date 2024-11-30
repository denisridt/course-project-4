<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json(['error' => 'Токен не предоставлен'], 401);
        }

        // Убираем "Bearer " из токена
        $token = str_replace('Bearer ', '', $token);

        // Находим пользователя по токену
        $user = User::where('api_token', $token)->first();

        if (!$user) {
            return response()->json(['error' => 'Недействительный токен'], 401);
        }

        // Возвращаем информацию о пользователе
        return response()->json(['user' => $user]);
    }

    public function update(UpdateProfileRequest $request)
    {
        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json(['error' => 'Токен не предоставлен'], 401);
        }

        // Убираем "Bearer " из токена
        $token = str_replace('Bearer ', '', $token);

        // Находим пользователя по токену
        $user = User::where('api_token', $token)->first();

        if (!$user) {
            return response()->json(['error' => 'Недействительный токен'], 401);
        }


        // Проверка, что пользователь существует
        if (!$user) {
            return response()->json(['error' => 'Пользователь не авторизован'], 401);
        }

        // Обновляем данные пользователя
        $user->update($request->only(['name', 'surname', 'patronymic', 'birthday', 'email', 'telephone']));

        return response()->json([
            'message' => 'Профиль успешно обновлен.',
            'user' => $user
        ]);
    }
}
