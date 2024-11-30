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

        // Загрузка файла изображения
        $imageName = time() . '.' . $request->photo->extension();


        // Создание нового товара в базе данных
        $products = new Product($request->all());
        $products->photo = 'storage/images/products/' . $imageName; // Путь до загруженного изображения
        $products->save();
        $request->photo->move(public_path('storage/images/products/'), $imageName);

        return response()->json(['message' => 'Товар успешно создан'], 201);
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
        $products = Product::find($id);
        if (!$products) {
            throw new ApiException('Товар не найден', 404);
        }
        // Проверяем, есть ли продукт с таким именем уже в базе данных
        $existingProduct = Product::where('name', $request->input('name'))->first();
        if ($existingProduct) {
            throw new ApiException('Продукт с таким именем уже существует', 422);
        }
        // Если файл был загружен, сохраняем его и обновляем путь к фото
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $imageName = time() . '.' . $photo->getClientOriginalExtension();

            $photo->storeAs('public/images/products', $imageName);
            $products->photo =  'storage/images/products/' . $imageName;
        }
        $products->fill($request->except('photo'));

        // Сохранение изменений в базе данных
        $products->save();

        // Перенаправление на страницу продукта с сообщением об успехе
        return response()->json(['message' => 'Продукт успешно обновлен'], 200);
    }
}
