<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name', 'surname', 'patronymic', 'login', 'password', 'birthday', 'email', 'telephone', 'role_id'
    ];

    // Список полей для преобразования
    protected function casts(): array
    {
        return [
            'password' => 'hashed', // Хэширование пароля
        ];
    }

    // Прячем поля, которые не должны быть возвращены в ответах
    protected $hidden = [
        'password', 'remember_token', // Убираем 'api_token' здесь, так как токены будут храниться в таблице personal_access_tokens
    ];

    protected static function booted()
    {
        static::creating(function ($user) {
            // Пример: задаем роль по умолчанию, если не указана
            if (!$user->role_id) {
                $user->role_id = 2; // Устанавливаем роль по умолчанию, например, роль с id = 1
            }
        });
    }

    // Аксессор для пароля, чтобы всегда хэшировать его при сохранении
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    // Отношение с ролью пользователя
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Отношение с адресами
    public function address()
    {
        return $this->hasOne(Address::class);
    }

    // Отношение с заказами
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Отношение с корзинами
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
}
