<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Http\Requests\CategoryCreateRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Models\Category;
use App\Models\Product;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return response(['data' => $categories,]);
    }

    public function show($id){
        $products = Product::where('category_id', $id)->get();
        return response(['data' => $products]);
    }

    public function create(CategoryCreateRequest $request)
    {
        try {
            // Проверяем, существует ли категория
            $existingCategory = Category::where('name', $request->input('name'))->first();
            if ($existingCategory) {
                return response()->json([
                    'error' => [
                        'message' => 'Категория с таким именем уже существует.',
                        'code'    => 422,
                        'category' => [
                            'id'   => $existingCategory->id,
                            'name' => $existingCategory->name,
                        ],
                    ],
                ], 422);
            }

            // Создаем новую категорию
            $category = Category::create([
                'name' => $request->input('name'),
            ]);

            return response()->json([
                'message' => 'Категория успешно создана.',
                'category' => [
                    'id'   => $category->id,
                    'name' => $category->name,
                    'created_at' => $category->created_at->toDateTimeString(),
                ],
            ], 201);
        } catch (\Exception $e) {
            // Обработка непредвиденных ошибок
            return response()->json([
                'error' => [
                    'message' => 'Ошибка при создании категории.',
                    'details' => $e->getMessage(),
                ],
            ], 500);
        }
    }


    public function destroy($id)
    {
        // Поиск категории по id
        $category = Category::find($id);

        // Если категория не найдена, возвращаем ошибку с подробностями
        if (!$category) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => 'Категория не найдена.',
                    'details' => 'Пожалуйста, проверьте правильность ID категории и попробуйте снова.'
                ]
            ], 404);
        }

        // Запоминаем название категории перед удалением
        $categoryName = $category->name;

        // Удаляем категорию
        $category->delete();

        // Возвращаем успешный ответ с информацией об удаленной категории
        return response()->json([
            'message' => 'Категория успешно удалена.',
            'category' => [
                'id' => $id,
                'name' => $categoryName,
                'deleted_at' => now(),
            ],
            'details' => 'Категория была удалена из базы данных.'
        ], 200);
    }

    public function update(CategoryUpdateRequest $request, $id)
    {
        // Поиск категории по id
        $category = Category::find($id);

        // Если категория не найдена, выводим ошибку
        if (!$category) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => 'Категория с таким ID не найдена.',
                    'details' => 'Проверьте правильность указанного ID категории.'
                ]
            ], 404);
        }

        // Проверка, существует ли категория с таким названием
        if ($category->name !== $request->input('name')) {
            $existingCategory = Category::where('name', $request->input('name'))->first();
            if ($existingCategory) {
                return response()->json([
                    'error' => [
                        'code' => 422,
                        'message' => 'Категория с таким названием уже существует.',
                        'details' => 'Пожалуйста, выберите уникальное название для категории.'
                    ]
                ], 422);
            }
        }

        // Обновляем название категории
        $category->name = $request->input('name');

        // Сохраняем изменения в базе данных
        $category->save();

        // Возвращаем успешный ответ с информацией о категории
        return response()->json([
            'message' => 'Категория успешно обновлена.',
            'category' => $category
        ], 200);
    }
}
