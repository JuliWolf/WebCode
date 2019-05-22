<?php

namespace App;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class User extends Model implements Authenticatable{
    //если название таблицы, в которую будут записываться данные пользователя не совпадают с установленным названием в миграции
//    protected $table = 'users2';

    use \Illuminate\Auth\Authenticatable;

    public function posts(){
        return $this->hasMany('App\Post');
    }

    public function likes(){
        return $this->hasMany('App\Like');
    }

}
