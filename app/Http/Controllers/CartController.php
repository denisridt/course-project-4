<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\CartResource; // Для возврата форматированного ответа

class CartController extends Controller
{
    // Просмотр корзины
    public function viewCart()
    {
        // Проверяем, авторизован ли пользователь
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Пользователь не авторизован'], 401);
        }

        // Получаем все товары в корзине пользователя
        $cartItems = Cart::where('user_id', $user->id)->get();
        // Возвращаем корзину в виде JSON

        return response()->json(['cartItems' => CartResource::collection($cartItems)]);
    }

    // Добавление товара в корзину
    public function addItem(Request $request, $id)
    {
        // Проверяем, авторизован ли пользователь через сессии
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Вы не авторизованы', 'code' => 401], 401);
        }

        // Получаем товар по ID
        $product = Product::findOrFail($id);

        // Получаем количество из запроса
        $quantity = $request->input('quantity', 1); // По умолчанию 1

        // Проверяем, есть ли товар в корзине пользователя
        $cartItem = Cart::where('user_id', $user->id)->where('product_id', $id)->first();

        if ($cartItem) {
            // Если товар уже есть в корзине, обновляем количество
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            // Если товара нет в корзине, добавляем новый элемент
            $cartItem = new Cart();
            $cartItem->user_id = $user->id;
            $cartItem->product_id = $id;
            $cartItem->quantity = $quantity;
            $cartItem->save();
        }

        // Рассчитываем итоговую цену
        $totalPrice = $product->price * $cartItem->quantity;

        // Возвращаем успешный ответ с данными
        return response()->json([
            'message' => 'Товар успешно добавлен в корзину',
            'total_price' => $totalPrice
        ]);
    }



    // Обновление товара в корзине
    public function update(Request $request)
    {
        // Проверяем, авторизован ли пользователь
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Пользователь не авторизован'], 401);
        }

        // Получаем данные из запроса
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');

        // Находим товар в корзине пользователя
        $cartItem = Cart::where('user_id', $user->id)->where('product_id', $productId)->first();

        if ($cartItem) {
            // Обновляем количество товара в корзине
            $cartItem->quantity = $quantity;
            $cartItem->save();

            // Возвращаем успешный ответ
            return response()->json(['message' => 'Количество товара обновлено']);
        }

        // Если товар не найден в корзине
        return response()->json(['error' => 'Товар не найден в корзине'], 404);
    }
}
