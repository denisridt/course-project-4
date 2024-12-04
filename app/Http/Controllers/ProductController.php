<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Http\Requests\ProductCreateRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Product;
use Illuminate\Http\Request;


class ProductController extends Controller
{
    public function  index(){
        $products = Product::all();

        return response(['data' => $products]);
    }

    public function show($id)
    {
        $products = Product::find($id);
        if (!$products) {
            throw new ApiException('Товар не найден', 404);
        }
        return response(['data' => $products]);
    }

    public function create(ProductCreateRequest $request)
    {
        // Проверяем, существует ли продукт с таким же именем (для предотвращения дубликатов)
        $existingProduct = Product::where('name', $request->input('name'))->first();

        if ($existingProduct) {
            return response()->json([
                'error' => [
                    'message' => 'Продукт с таким названием уже существует.',
                    'code'    => 422,
                    'product' => [
                        'id'    => $existingProduct->id,
                        'name'  => $existingProduct->name,
                        'price' => $existingProduct->price,
                    ],
                ],
            ], 422);
        }

        // Загрузка файла изображения (если есть)
        $imagePath = null;
        if ($request->hasFile('photo')) {
            $imageName = time() . '.' . $request->photo->extension();
            $imagePath = 'storage/images/products/' . $imageName;
            $request->photo->move(public_path('storage/images/products/'), $imageName);
        }

        // Создание нового продукта
        $product = Product::create([
            'name'        => $request->input('name'),
            'description' => $request->input('description'),
            'price'       => $request->input('price'),
            'quantity'    => $request->input('quantity'),
            'photo'       => $imagePath,
            'category_id' => $request->input('category_id'),
        ]);

        return response()->json([
            'message' => 'Продукт успешно создан.',
            'product' => [
                'id'          => $product->id,
                'name'        => $product->name,
                'description' => $product->description,
                'price'       => $product->price,
                'quantity'    => $product->quantity,
                'photo_url'   => $imagePath ? asset($imagePath) : null,
                'category_id' => $product->category_id,
                'created_at'  => $product->created_at->toDateTimeString(),
            ],
        ], 201);
    }



    public function destroy($id){
        $product = Product::find($id);
        if (!$product) {
            throw new ApiException('Продукт не найден', 404);
        }
        $product->delete();
        return response()->json(['message' => 'Продукт успешно удален'], 200);
    }

    public function update(ProductUpdateRequest $request, $id)
    {
        // Поиск продукта по id
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => 'Продукт с таким ID не найден.',
                    'details' => 'Убедитесь, что вы указали правильный ID продукта.',
                ]
            ], 404);
        }

        // Проверяем, есть ли продукт с таким названием уже в базе данных (если название изменено)
        if ($request->has('name') && $request->input('name') !== $product->name) {
            $existingProduct = Product::where('name', $request->input('name'))->first();
            if ($existingProduct) {
                return response()->json([
                    'error' => [
                        'code' => 422,
                        'message' => 'Продукт с таким названием уже существует.',
                        'details' => 'Пожалуйста, выберите уникальное название для продукта.',
                    ]
                ], 422);
            }
        }

        // Обработка загруженного фото (если есть)
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $imageName = time() . '.' . $photo->getClientOriginalExtension();

            // Сохранение изображения в папку
            $photo->storeAs('public/images/products', $imageName);
            $product->photo = 'storage/images/products/' . $imageName;
        }

        // Обновление других полей
        $product->fill($request->except('photo'));

        // Сохранение изменений в базе данных
        $product->save();

        return response()->json([
            'message' => 'Продукт успешно обновлен.',
            'product' => $product,
        ], 200);
    }
}
