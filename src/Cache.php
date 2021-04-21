<?php
namespace Score;
/**
 * Created by PhpStorm.
 * User: dawnlight
 * Date: 2019/4/24
 * Time: 11:33 AM
 */
class Cache{
    /**
     * dawnlight 2019/4/24 11:35 AM
     * @param $key
     * @param $data
     * @param int $timeout 如果是0表示不会超时 单位是秒
     */
    public static $cacheDir = APPLICATION_PATH."/storage/data/cache/";

    public static function set($key,$data,$timeout = 0){
        if(!config("application.is_cache")){
            return [];
        }
        if (!file_exists(self::$cacheDir)){
            mkdir (self::$cacheDir,0777,true);
        }

        //获取存储时的key名
        $putKey = md5($key);
        $filename = self::$cacheDir.$putKey;
        file_put_contents($filename,json_encode($data),FILE_USE_INCLUDE_PATH);

        self::setKeyTimeout($putKey,$timeout);

        return true;

    }

    public static function get($key,$default=[]){
        if(!config("application.is_cache")){
            return [];
        }
        $putKey = md5($key);
        
        $filename = self::$cacheDir.$putKey;

        if(file_exists($filename)){
            $filetime = filectime($filename);

            $timeout = self::getKeyTimeout($putKey);

            if(($filetime+$timeout) < time()) {//检查是否超时 超时了就把文件删了返回null
                unlink($filetime);
            }else{
                $data = json_decode(file_get_contents($filename),1);
                return $data;
            }

        }

        if($default==[]){
            return false;
        }

        return $default;
    }

    /**
     * dawnlight 2019/4/24 1:30 PM
     * 获取超时时间
     */
    public static function getKeyTimeout($key){

        $filename = self::$cacheDir."keytimeout";

        if(file_exists($filename)){
            $data = json_decode(file_get_contents($filename),1);
        }else{
            return false;
        }

        if(isset($data[$key])){
            return $data[$key];
        }

        return false;
    }

    public static function setKeyTimeout($key,$timeout){
        $data = [];

        $filename = self::$cacheDir."keytimeout";

        if(file_exists($filename)){
            $data = json_decode(file_get_contents($filename),1);
        }

        $data[$key] = $timeout;

        file_put_contents($filename,json_encode($data),FILE_USE_INCLUDE_PATH);

        return true;
    }

    public static function flushAll(){
        //删除文件

        self::deldir(self::$cacheDir);
    }

    public static  function deldir($path){
        //如果是目录则继续
        if(is_dir($path)){
            //扫描一个文件夹内的所有文件夹和文件并返回数组
            $p = scandir($path);
            foreach($p as $val){
                //排除目录中的.和..
                if($val !="." && $val !=".."){
                    //如果是目录则递归子目录，继续操作
                    if(is_dir($path.$val)){
                        //子目录中操作删除文件夹和文件
                        deldir($path.$val.'/');
                        //目录清空后删除空文件夹
                        @rmdir($path.$val.'/');
                    }else{
                        //如果是文件直接删除
                        unlink($path.$val);
                    }
                }
            }
        }
    }
}