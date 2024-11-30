<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AddressController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Регистрация
Route::post('/register' , [AuthController::class, 'register' ]);
//Авторизация
Route::post('/login' , [AuthController::class, 'login' ]);
//Выход
Route::middleware('auth:api')->get('/logout', [AuthController::class, 'logout']);

//Просмотр всех товаров
Route::get('/products',[ProductController::class, 'index']);
//Просмотр конкретного товара
Route::get('/products/{id}' , [ProductController::class, 'show']);
//Просмотр категорий
Route::get('/categories',[CategoryController::class, 'index']);
//Просмотр товаров определенной категории
Route::get('/categories/{id}',[CategoryController::class, 'show']);

Route::middleware(['auth:api','role:admin'])->group(function () {

    //Продукты

    //Создание продукта
    Route::post('/products/create',[ProductController::class, 'create']);
    //Редактирование продукта
    Route::patch('/products/update/{id}',[ProductController::class, 'update']);
    //Удаление продукта
    Route::delete('/products/destroy/{id}',[ProductController::class, 'destroy']);

    //Категории

    //Создание категории
    Route::post('/categories/create',[CategoryController::class, 'create']);
    //Редактирование категории
    Route::patch('/categories/{id}',[CategoryController::class, 'update']);
    //Удаление категории
    Route::delete('/categories/destroy/{id}',[CategoryController::class, 'destroy']);
});


Route::middleware(['auth:api','role:user|admin'])->group(function (){

    //Добавление товара в корзину
    Route::middleware('auth:api')->post('/products/{id}', [CartController::class, 'addItem']);

    //Просмотр корзины
    Route::middleware('auth:api')->get('/cart', [CartController::class, 'viewCart']);

    //Редактирование корзины
    Route::middleware('auth:api')->patch('/cart', [CartController::class, 'update']);

    //Просмотр профиля
    Route::middleware('auth:api')->get('/profile', [ProfileController::class, 'show']);

    //Редактирование профиля
    Route::middleware('auth:api')->put('/profile/edit', [ProfileController::class, 'update']);

    //Оформление заказа
    Route::middleware('auth:api')->post('/checkout', [OrderController::class, 'checkout']);

    //Показать адрес пользователя
    Route::middleware('auth:api')->get('/address', [AddressController::class, 'show']);

    //Создать или обновить адрес пользователя
    Route::middleware('auth:api')->post('/address', [AddressController::class, 'store']);

});
