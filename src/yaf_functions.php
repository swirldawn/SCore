<?php
/**
 * 扩展依赖部分,可添加自定义函数，不可删除
 */

function config($key,$default_value=""){

    $app_config = new \Yaf\Config\Ini(APPLICATION_PATH . '/configs/application.ini',ini_get('yaf.environ') );

    $realKey = explode(".",$key);

    $re = $app_config->get($realKey[0]);

    if(isset($realKey[1])){
        if(isset($realKey[2])){
            $re = $re[$realKey[1]][$realKey[2]];
        }else{
            $re = $re[$realKey[1]];
        }
    }

    if(empty($re)){
        return $default_value;
    }

    return $re;
}


function response_json($data){
    header ( 'Content-type: application/json' );
    echo json_encode ( $data );
    Yaf\Dispatcher::getInstance ()->autoRender ( FALSE );
    exit ();
}
/**
 * 设置或获取session
 * @param $key
 * @param null $value
 * @return mixed
 */
function session($key=''){

    $app_session = Yaf\Session::getInstance();

    if(is_array($key)){
        return $app_session->set(key($key),array_shift($key));
    }else{
        if($key==''){
            $re = $app_session->get();
        }else{
            $realKey = explode(".",$key);

            $re = $app_session->get($realKey[0]);

            if(isset($realKey[1])){

                $re = $re[$realKey[1]];
            }
        }

        return $re;
    }

}

function session_flush(){

    $app_session = Yaf\Session::getInstance();

    $data = session();
    $re = true;
    foreach($data as $key=>$value){
        $re = $app_session->del($key);
    }
    return $re;
}

/**
 * 获取请求参数
 * @param null $key
 * @param string $default_value
 * @return string
 */
function request($key=null,$default_value=""){
    $params = array_merge($_GET,$_POST);

    $json= json_decode(file_get_contents("php://input"),1);
    
    if($json){
        $params = array_merge($params,$json);
    }


    foreach($params as $k=>$v){
        if(gettype($v) == "string"){
            $params[$k] = trim($v);
        }
    }

    if($key!=null){
        return isset($params[$key])?$params[$key]:$default_value;
    }

    return $params;
}
/**
 * 获取 $_COOKIE
 * @param String $name
 * @return String
 */
function get_cookie($name)
{
    return isset($_COOKIE[$name]) ? $_COOKIE[$name] : '';
}

/**
 * 设置cookie
 *
 * @param String $name
 * @param String $domain
 * @param Mixed $value
 * @param Integer $expire (0:Session、-1:删除、time():过期时间 )
 * @param String $path
 * @param bool $httponly
 */
function set_cookie($name, $value, $domain, $expire = 0, $path = "/", $httponly = false, $secureAuto = false)
{
    if ($secureAuto == false) {
        $secure = $_SERVER['SERVER_PORT'] == 443 ? true : false;
    } else {
        $secure = true;
    }
    if ($expire == 0) {
        $expire = 0;
    } else if ($expire == -1) {
        $expire = time() - 3600;
    }
   return setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
}

/**
 * 调试输出
 * dawnlight 2019/1/2 2:49 PM
 * @param mixed ...$data<script src="https://cdnjs.loli.net/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
 */
function dd(...$data){
    echo "    <!DOCTYPE html>
    <html lang=\"en\">
    <head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <meta http-equiv=\"X-UA-Compatible\" content=\"ie=edge\">
    <script src=\"https://cdnjs.loli.net/ajax/libs/jquery/3.2.1/jquery.min.js\"></script>
    <script src=\"https://lib.baomitu.com/highlight.js/9.13.1/highlight.min.js\"></script>
    <link href=\"https://lib.baomitu.com/highlight.js/9.13.1/styles/default.min.css\" rel=\"stylesheet\"> 
    <link href=\"https://lib.baomitu.com/highlight.js/9.13.1/styles/atelier-dune-light.min.css\" rel=\"stylesheet\">
    </head>
    <body>
    <pre style='font-size:16px;'>";
    var_dump($data);
    echo "</pre><script>
    $('pre').each(function(i, block) {
            hljs.highlightBlock(block);
        });
        </script>
        </body></html>";
    die;
}

/**
 * 表单验证
 * dawnlight 2019/1/2 3:53 PM
 * @param $params
 * @param $roles
 * @param null $errorMsg
 * @return array
 */
function validator($params,$roles,$errorMsg=null){
    return Validator::validate($params,$roles,$errorMsg);
}


function get_real_ip(){
    $ip=false;
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    if(!empty($_SERVER['HTTP_X_REAL_IP'])){
        $ip=$_SERVER['HTTP_X_REAL_IP'];
    }
    if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        $ips=explode (', ', $_SERVER['HTTP_X_FORWARDED_FOR']);
        if($ip){ array_unshift($ips, $ip); $ip=FALSE; }
        for ($i=0; $i < count($ips); $i++){
            if(!eregi ('^(10│172.16│192.168).', $ips[$i])){
                $ip=$ips[$i];
                break;
            }
        }
    }
    return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
}
