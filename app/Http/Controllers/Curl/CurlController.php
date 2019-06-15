<?php

namespace App\Http\Controllers\Curl;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;

class CurlController extends Controller
{
    //curlpost POST请求
    public function curlPost()
    {
        //数据形式1form-data
        $post_data = [
            'name'=>'zhangsan',
            'pwd'=>'123'
        ];

        //数据形式2x-www-form-urlencoded
        //$post_data = "name=zhangsan&pwd=123";

        //1、初始化
        $url = "http://www.1810api.com/curl3";
        $ch = curl_init($url);

        //2、设置参数
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,false);
        curl_setopt($ch,CURLOPT_POST,true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data);

        //3、执行会话 发送请求
        curl_exec($ch);

        //获取错误码、错误信息
        $errno = curl_errno($ch);
        $errinfo = curl_error($ch);

        var_dump($errno);echo "<hr>";
        var_dump($errinfo);

        //4、关闭会话
        curl_close($ch);
    }

    //curlget  GET请求
    public function curlGet()
    {
        //1、初始化
        $url = "http://www.baidu.com";
        $ch = curl_init($url);

        //2、设置参数
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,0);   //true即为1，不显示视图页面；false即为0，显示视图

        //3、执行会话
        curl_exec($ch);

        //4、关闭会话
        curl_close($ch);

    }

    public function curl3()
    {
        echo '<pre>';print_r($_POST);echo '</pre>';
    }

    //获取access_token
    public function getToken()
    {
        $access_token = Cache::get('access_token');
        if(empty($access_token)){
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx2c1c924fdc4a3618&secret=d70f29437470a34e3db8d6162811c76f";
            $ch = curl_init($url);
            //2、参数设置
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
            //3、执行会话 处理数据
            $data = curl_exec($ch);
            //4、关闭会话
            curl_close($ch);

            $data = json_decode($data,true);
            $access_token = $data['access_token'];
            Cache::put('access_token',$access_token,7200);
        }
        return $access_token;
    }

    //创建自定义菜单
    public function createMenu()
    {
        $access_token = $this->getToken();
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$access_token}";

        $post_data =  '{
             "button":[
             {    
                  "type":"click",
                  "name":"查表白",
                  "key":"select"
              },
              {    
                  "type":"click",
                  "name":"发表白",
                  "key":"send"
              }
              ]
         }';

        //1、初始化
        $ch = curl_init($url);

        curl_setopt($ch,CURLOPT_RETURNTRANSFER,false);
        curl_setopt($ch,CURLOPT_POST,true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data);

        $data = curl_exec($ch);

        curl_close($ch);

        var_dump($data);die;
    }

    //文件上传
    public function upload()
    {
        return view('curl.upload');
    }
    public function getupload()
    {
        echo '<pre>';print_r(file_get_contents("php://input"));echo '</pre>';
    }
    public function uploadPost()
    {
        $data = \request()->post();
        if(\request()->hasfile('pic')){
            if (\request()->file('pic')->isValid()) {
                $photo = \request()->file('pic');
                $extension = $photo->getClientOriginalExtension(); //获取后缀

                $store_result = $photo->storeAs('uploads/'.date('Ymd'), date('Ymd').rand(100,999).'.'.$extension);
            }
            //简单上传
            //$post['s_img']->file->store('uploads');
            $data['pic'] = $store_result;
            $file = public_path()."/uploads/".$data['pic'];

            $url = "http://www.1810api.com/getupload";
            $ch = curl_init($url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,false);
            curl_setopt($ch,CURLOPT_POST,true);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$file);
            $res = curl_exec($ch);

            curl_close($ch);
        }
    }

    //测试form1
    public function form1()
    {
        return view('curl.form1');
    }
    public function formPost()
    {
        echo '<pre>';print_r($_POST);echo '</pre>';
    }

    //对称加密
    public function encrypt1()
    {
        $data = 'hello world';

        $enc_data = base64_encode($data);
        $client = new Client();
        $url = "http://www.lumen.com/test/decrypt1";
        $response  =  $client->request('POST',$url,[
            'body'=>$enc_data
        ]);

        echo "<hr>";
        echo $response->getBody();
    }

    public function encrypt2()
    {
        $data = "一程山路";   //原始数据
        $key = "password";     //加密解密密码
        $iv = "abcabcabcabcabca";    //初始向量  16字节

        $enc_data = openssl_encrypt($data,'AES-128-CBC',$key,OPENSSL_RAW_DATA,$iv);
        $client = new Client();
        $url = "http://www.lumen.com/test/decrypt2";
        $response = $client->request('POST',$url,[
           'body'=>base64_encode($enc_data)
        ]);
        echo base64_encode($enc_data);
        echo "<hr>";
        echo $response->getBody();
    }

    //非对称加密
    public function rsa1()
    {
        $data = "一程山路";
        //使用非对称加密 私钥加密数据
        //获取私钥
        $private_key = openssl_get_privatekey("file://".public_path("keys/private.pem"));
        //dd($private_key);
        //加密数据
        openssl_private_encrypt($data,$enc_data,$private_key);

        $client = new Client();
        $url = "http://www.lumen.com/test/rsadecrapy1";
        $response = $client->request('POST',$url,[
            'body'=>$enc_data
        ]);
        echo $response->getBody();
    }


    /**
     * 20190613作业练习
     *  1 发送端使用 对称加密方式加密数据 并使用私钥生成签名，将加密后的数据与签名发送给接收端
    2 接收端验证签名并解密数据，验签失败提示错误信息，验签成功后，使用对称加密加密数据，并使用私钥签名，将数据返回给发送端。
    3 发送端收到数据后，验证签名并解密数据
     */
    public function rsaencrypt2()
    {
        //原始数据对称加密
        $data = "hello world";
        $key = "password";
        $iv = "abcabcabcabcabca";

        $enc_data = openssl_encrypt($data,'AES-128-CBC',$key,OPENSSL_RAW_DATA,$iv);

        //私钥生成签名
        $private_key = openssl_get_privatekey("file://".public_path('keys/private.pem'));
        openssl_sign($data, $signature, $private_key);

        //echo $signature;die;
        $arr = [
            'enc_data'=>$enc_data,
            'signature'=>$signature
        ];
        $body_data = serialize($arr);
        $client = new Client();
        $url = "http://www.lumen.com/test/rsadecrapy2";
        $response = $client->request('POST',$url,[
            'body'=>$body_data
        ]);
        //openssl_free_key($private_key);

        echo "<hr>";
        echo $response->getBody();
    }

    public function rsaencrypt3()
    {
        $data = file_get_contents("php://input");
        $body_data = unserialize($data);
        $enc_data = $body_data['enc_data'];
        $signature = $body_data['signature'];

        $key = "password";
        $iv = "abcabcabcabcabca";
        $dec_data = openssl_decrypt($enc_data,'AES-128-CBC',$key,OPENSSL_RAW_DATA,$iv);

        $public_key = openssl_get_publickey("file://".public_path('keys/pub.key'));
        $ok = openssl_verify($dec_data, $signature, $public_key);

        if($ok == 1){
            echo $dec_data;
        }elseif ($ok == 0) {
            echo "bad";
        } else {
            echo "ugly, error checking signature";
        }
    }

    /**
     * 签名20190614
     */
    public function openssltest1()
    {
        //原始数据
        $data = [
            'order_id'=>12121212,
            'order_amount'=>1212,
            'add_time'=>time(),
            'uid'=>12
        ];
        echo '<pre>';print_r($data);echo '</pre>';

        //1、排序
        ksort($data);
        //2、拼接成字符串
        $str = "";
        foreach ($data as $k=>$v){
            $str .= $k.'='.$v.'&';
        }
        echo $str;echo "<hr>";

        $str0 = rtrim($str,'&');
        echo $str0;echo "<hr>";

        //3、生成签名
        openssl_sign($str0,$sign,openssl_get_privatekey("file://".public_path('keys/private.pem')));
        echo '<pre>';print_r($sign);echo '</pre>';
        $signature = base64_encode($sign);    //生成的签名
        echo '<pre>';print_r($signature);echo '</pre>';

        //发送数据
        $data['signature'] = $signature;
        echo '<pre>';print_r($data);echo '</pre>';
        $url = "http://www.lumen.com/test/openssltest1";
        $client = new Client();
        $response = $client->request('POST',$url,[
            'form_params' => $data
        ]);
        echo "<hr>";
        echo $response->getBody();
    }

    /**
     * 支付宝手机支付
     */
    public function pay()
    {
        return view('pay.alipay');
    }
    public function alipay()
    {
        $app_id = "2016092700609145";
        //支付宝网关
        $alipay_gateway = "https://openapi.alipaydev.com/gateway.do";

        $biz_content = [
            'subject' => '订单测试'.rand(1111,9999).time(),
            'out_trade_no' => mt_rand(1111,9999).time(),
            'total_amount' => mt_rand(1,100000),
            'product_code' => "QUICK_WAP_WAY",
        ];

        $data = [
            'app_id' => $app_id,
            'method' => 'alipay.trade.wap.pay',
            'charset' => 'utf-8',
            'sign_type' => 'RSA2',
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => '1.0',
            'biz_content' => json_encode($biz_content)
        ];

        ksort($data);
        $str0 = "";
        foreach ($data as $k=>$v){
            $str0 .= $k .'='. $v.'&';
        }
        $str = rtrim($str0,'&');
        $private_key = openssl_get_privatekey("file://".public_path('keys/private.pem'));
        //dump($private_key);
        //echo $str;die;
        openssl_sign($str,$signature,$private_key,OPENSSL_ALGO_SHA256);

        $data['sign'] = base64_encode($signature);
        //dd($data);
        // 4 urlencode
        $param_str = '?';
        foreach($data as $k=>$v){
            $param_str .= $k.'='.urlencode($v) . '&';
        }
        //dd($param_str);
        $param = rtrim($param_str,'&');
        $url = $alipay_gateway . $param;
        //发送GET请求
        header("Location:".$url);

    }

}
