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
        // Проверяем, авторизован ли пользователь
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'message' => 'Вы не авторизованы',
                'code' => 401,
                'details' => 'Пожалуйста, выполните вход в систему для добавления товаров в корзину.'
            ], 401);
        }

        // Получаем товар по ID
        $product = Product::findOrFail($id);

        // Получаем количество из запроса, по умолчанию 1
        $quantity = $request->input('quantity', 1);

        // Проверяем, есть ли товар в корзине пользователя
        $cartItem = Cart::where('user_id', $user->id)->where('product_id', $id)->first();

        if ($cartItem) {
            // Если товар уже есть в корзине, обновляем количество
            $cartItem->quantity += $quantity;
            $cartItem->save();

            // Рассчитываем итоговую цену
            $totalPrice = $product->price * $cartItem->quantity;
            $message = 'Товар успешно обновлен в вашей корзине';
        } else {
            // Если товара нет в корзине, добавляем новый элемент
            $cartItem = new Cart();
            $cartItem->user_id = $user->id;
            $cartItem->product_id = $id;
            $cartItem->quantity = $quantity;
            $cartItem->save();

            // Рассчитываем итоговую цену
            $totalPrice = $product->price * $cartItem->quantity;
            $message = 'Товар успешно добавлен в корзину';
        }

        // Получаем все товары в корзине
        $cartItems = Cart::where('user_id', $user->id)->get();
        $totalQuantity = $cartItems->sum('quantity'); // Общее количество товаров в корзине
        $cartTotalPrice = $cartItems->sum(function ($item) {
            return $item->quantity * $item->product->price;
        }); // Общая стоимость корзины

        // Возвращаем успешный ответ с подробной информацией
        return response()->json([
            'message' => $message,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity_added' => $cartItem->quantity,
                'total_price_for_product' => $totalPrice,
            ],
            'cart' => [
                'total_items' => $totalQuantity,
                'total_price' => $cartTotalPrice,
                'currency' => 'руб.',  // Можно заменить на динамическую валюту, если нужно
            ],
            'details' => 'Товар успешно добавлен или обновлен в корзине. Вы можете продолжить покупки или перейти к оформлению заказа.'
        ], 200);
    }


    // Обновление товара в корзине
    public function update(Request $request)
    {
        // Проверяем, авторизован ли пользователь
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'error' => 'Пользователь не авторизован',
                'message' => 'Для обновления товаров в корзине, необходимо войти в систему.',
                'code' => 401
            ], 401);
        }

        // Получаем данные из запроса
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');

        // Проверяем, что количество больше нуля
        if ($quantity <= 0) {
            return response()->json([
                'error' => 'Некорректное количество товара',
                'message' => 'Количество товара должно быть больше нуля.',
                'code' => 400
            ], 400);
        }

        // Находим товар в корзине пользователя
        $cartItem = Cart::where('user_id', $user->id)->where('product_id', $productId)->first();

        if ($cartItem) {
            // Обновляем количество товара в корзине
            $cartItem->quantity = $quantity;
            $cartItem->save();

            // Получаем товар из базы данных для отображения деталей
            $product = Product::find($productId);
            if (!$product) {
                return response()->json([
                    'error' => 'Товар не найден',
                    'message' => 'Продукт с таким ID не найден в базе данных.',
                    'code' => 404
                ], 404);
            }

            // Рассчитываем итоговую стоимость товара с обновленным количеством
            $totalPrice = $product->price * $cartItem->quantity;

            // Получаем все товары в корзине
            $cartItems = Cart::where('user_id', $user->id)->get();
            $totalQuantity = $cartItems->sum('quantity'); // Общее количество товаров в корзине
            $cartTotalPrice = $cartItems->sum(function ($item) {
                return $item->quantity * $item->product->price;
            }); // Общая стоимость корзины

            // Возвращаем успешный ответ с подробной информацией
            return response()->json([
                'message' => 'Количество товара в корзине успешно обновлено',
                'updated_product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'quantity_updated' => $cartItem->quantity,
                    'unit_price' => $product->price,
                    'total_price_for_product' => $totalPrice,
                ],
                'cart' => [
                    'total_items' => $totalQuantity,
                    'total_price' => $cartTotalPrice,
                    'currency' => 'руб.',  // Можно заменить на динамическую валюту
                ],
                'details' => 'Вы успешно обновили количество товара в своей корзине. Вы можете продолжить покупки или оформить заказ.'
            ], 200);
        }

        // Если товар не найден в корзине
        return response()->json([
            'error' => 'Товар не найден в корзине',
            'message' => 'Убедитесь, что товар был добавлен в корзину перед обновлением.',
            'code' => 404
        ], 404);
    }
}
