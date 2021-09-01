<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

     protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function statuses()
    {
        return $this->hasMany(Status::class)->orderBy('order');
    }

    protected static function booted()
    {
        static::created(function ($user) {
            $user->statuses()->createMany([
                [
                    'title' => '未処理',
                    'slug' => 'backlog',
                    'order' => 1
                ],
                [
                    'title' => '着手',
                    'slug' => 'up_next',
                    'order' => 2
                ],
                [
                    'title' => '進行中',
                    'slug' => 'progress',
                    'order' => 3
                ],
                [
                    'title' => '完了',
                    'slug' => 'done',
                    'order' => 4
                ],
                [
                    'title' => '保留',
                    'slug' => 'on_hold',
                    'order' => 5
                ]
            ]);
        });
    }
}
