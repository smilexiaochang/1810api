<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\UserModel;
use Illuminate\Support\Facades\Redis;

class TestController extends Controller
{
    //
    public function add()
    {
        Redis::set('name','zhangsan');
        dd(Redis::get('name'));
        die;
        $data = [
            'uname'=>'zhangsan',
            'upwd'=>'1234'
        ];
        $inserId = UserModel::insertGetId($data);
        dd($inserId);
    }
}
