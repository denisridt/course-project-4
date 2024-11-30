<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function show()
    {
        $user = auth()->user();

        // Проверяем, авторизован ли пользователь
        if (!$user) {
            return response()->json(['error' => 'Пользователь не авторизован'], 401);
        }

        // Жадная загрузка адреса
        $user->load('address');

        // Если адрес не найден, возвращаем ошибку
        if (!$user->address) {
            return response()->json(['error' => 'Адрес не найден'], 404);
        }

        return response()->json(['address' => $user->address]);
    }


    public function store(AddressRequest $request)
    {
        // Валидация уже выполнена в AddressRequest

        $user = auth()->user(); // Получаем текущего пользователя

        // Если у пользователя уже есть адрес, обновляем его, иначе создаем новый
        $address = $user->address ? $user->address : new Address();

        $address->city = $request->city;
        $address->street = $request->street;
        $address->house = $request->house;
        $address->floor = $request->floor;
        $address->apartment = $request->apartment;
        $address->entrance = $request->entrance;
        $address->intercom = $request->intercom;
        $address->comment = $request->comment;
        $address->user_id = $user->id;

        $address->save(); // Сохраняем данные

        return response()->json(['message' => 'Адрес успешно сохранен']);
    }
}
