<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('test','TestController@add');

//测试curl
Route::get('curlget','Curl\CurlController@curlGet');
Route::get('curlpost','Curl\CurlController@curlPost');
Route::get('gettoken','Curl\CurlController@getToken');
Route::get('/createmenu','Curl\CurlController@createMenu');
Route::get('/upload','Curl\CurlController@upload');
Route::post('/getupload','Curl\CurlController@getupload');
Route::post('/upload','Curl\CurlController@uploadPost');

Route::post('curl3','Curl\CurlController@curl3');

Route::get('/form1','Curl\CurlController@form1');
Route::post('/form1','Curl\CurlController@formPost');

//对称加密
Route::get('/encrypt1','Curl\CurlController@encrypt1');
Route::get('/encrypt2','Curl\CurlController@encrypt2');

//非对称加密
Route::get('/rsa1','Curl\CurlController@rsa1');

//20190613作业练习
Route::get('/rsaencrypt2','Curl\CurlController@rsaencrypt2');
Route::post('/test/rsadecrapy3','Curl\CurlController@rsaencrypt3');

//20190614签名
Route::get('/test/openssltest1','Curl\CurlController@openssltest1');


//支付宝测试
Route::get('/pay/alipay','Curl\CurlController@pay');   //视图页面
Route::get('/pay/go','Curl\CurlController@alipay');    //执行