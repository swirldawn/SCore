<?php
namespace Score;
/**
 * Created by PhpStorm.
 * User: dawnlight
 * Date: 2019/4/15
 * Time: 3:08 PM
 */
class Http{


    public static function get($url){

        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
        $data = curl_exec($curl);//返回api的json对象
        //关闭URL请求
        curl_close($curl);
        return $data;
    }
    /**
     * form格式请求
     * application/x-www-form-urlencoded
     * dawnlight 2018/12/13 下午5:18
     * @param $url
     * @param array $post
     * @param array $options
     * @return mixed|string
     */
    public static function post($url, $post = array(), $options = array()){
        $defaults = array(
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_URL => $url,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_POSTFIELDS => http_build_query($post)
        );
        $ch = curl_init();
        curl_setopt_array($ch, ($options + $defaults));
        if( ! $result = curl_exec($ch)){
            $result = curl_error($ch);
        }
        curl_close($ch);
        return $result;
    }

    /**
     * json格式请求数据在body体内
     * application/json
     * dawnlight 2018/12/13 下午5:18
     * @param $url
     * @param $data_string
     * @param array $header
     * @return array
     */
    public static function postJson($url, $data_string,$header=[]) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        if(!empty($header)){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($data_string))
        );
        ob_start();
        curl_exec($ch);
        $return_content = ob_get_contents();
        ob_end_clean();

        $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        return array($return_code, $return_content);
    }
}