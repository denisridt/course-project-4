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
            return response()->json([
                'error' => 'Пользователь не авторизован',
                'message' => 'Для просмотра адреса нужно быть авторизованным.',
                'code' => 401
            ], 401);
        }

        // Жадная загрузка адреса
        $user->load('address');

        // Если адрес не найден, возвращаем ошибку
        if (!$user->address) {
            return response()->json([
                'error' => 'Адрес не найден',
                'message' => 'У вас еще нет сохраненного адреса.',
                'code' => 404
            ], 404);
        }

        return response()->json([
            'message' => 'Адрес успешно найден',
            'address' => $user->address
        ]);
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

        // Возвращаем успешный ответ с подробностями
        return response()->json([
            'message' => $address->exists ? 'Адрес успешно обновлен' : 'Адрес успешно добавлен',
            'address' => [
                'city' => $address->city,
                'street' => $address->street,
                'house' => $address->house,
                'floor' => $address->floor,
                'apartment' => $address->apartment,
                'entrance' => $address->entrance,
                'intercom' => $address->intercom,
                'comment' => $address->comment
            ],
            'details' => 'Ваш адрес был успешно сохранен или обновлен. Вы можете редактировать его в любое время.',
            'code' => 200
        ]);
    }
}
