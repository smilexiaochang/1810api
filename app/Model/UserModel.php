<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
    protected $table = 'user';  //表名
    protected $primaryKey  = 'uid';  //主键
    public $timestamps = false;  //开启自动写入时间戳

}
