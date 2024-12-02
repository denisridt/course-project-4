<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function show(Request $request, $orderId)
    {
        // Получаем текущего пользователя
        $user = auth()->user();

        // Получаем заказ по его идентификатору и пользователю
        $order = Order::where('id', $orderId)
            ->where('user_id', $user->id)
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Заказ не найден'], 404);
        }

        // Получаем детали заказа с продуктами
        $orderDetails = OrderItem::where('order_id', $order->id)
            ->with('product')
            ->get();

        // Формируем данные для чека
        $checkData = [
            'order_id' => $order->id,
            'order_date' => $order->created_at,
            'order_details' => $orderDetails->map(function ($detail) {
                return [
                    'product_name' => $detail->product->name,
                    'quantity' => $detail->quantity,
                    'price' => $detail->price,
                    'total' => $detail->quantity * $detail->price,
                ];
            }),
            'total_amount' => $orderDetails->sum(function ($detail) {
                return $detail->quantity * $detail->price;
            }),
        ];

        return response()->json(['check' => $checkData]);
    }
    public function checkout(CheckoutRequest $request)
    {
        $user = auth()->user(); // Получаем текущего авторизованного пользователя

        // Проверяем, есть ли у пользователя адрес
        if (!$user || !$user->address) {
            return response()->json(['error' => 'Необходимо заполнить адрес для оформления заказа'], 400);
        }

        // Получаем товары из корзины пользователя
        $carts = Cart::where('user_id', $user->id)->get();

        if ($carts->isEmpty()) {
            return response()->json(['error' => 'Ваша корзина пуста'], 400);
        }

        // Проверяем, достаточно ли товара на складе для каждого продукта в корзине
        $insufficientProducts = [];
        foreach ($carts as $cart) {
            $product = Product::find($cart->product_id);

            if (!$product) {
                return response()->json(['error' => 'Продукт с ID ' . $cart->product_id . ' не найден'], 404);
            }

            if ($product->quantity < $cart->quantity) {
                $insufficientProducts[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'available_quantity' => $product->quantity,
                    'requested_quantity' => $cart->quantity,
                ];
            }
        }

        // Если есть продукты с недостаточным количеством, вернуть ошибку
        if (!empty($insufficientProducts)) {
            return response()->json([
                'error' => 'Недостаточно товара на складе для следующих продуктов',
                'products' => $insufficientProducts,
            ], 400);
        }

        // Создаем новый заказ
        $order = new Order();
        $order->user_id = $user->id;
        $order->address_id = $user->address->id;
        $order->dateOrder = now();
        $order->save();

        // Массив для сохранения деталей заказа
        $orderItems = [];

        // Создаем детали заказа и обновляем количество товара
        foreach ($carts as $cart) {
            $product = Product::find($cart->product_id);

            // Создаем новый объект OrderItem
            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $cart->product_id;
            $orderItem->quantity = $cart->quantity;
            $orderItem->price = $product->price;
            $orderItem->save();

            // Обновляем количество товара в таблице products
            $product->quantity -= $cart->quantity;
            $product->save();

            // Добавляем детали товара в массив
            $orderItems[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $cart->quantity,
                'price' => $product->price,
                'total_price' => $product->price * $cart->quantity,
            ];
        }

        // Удаляем товары из корзины
        Cart::where('user_id', $user->id)->delete();

        // Возвращаем успешный ответ с деталями заказа
        return response()->json([
            'message' => 'Заказ успешно оформлен',
            'order_id' => $order->id,
            'address' => $user->address,
            'order_items' => $orderItems,
        ], 200);
    }




}
