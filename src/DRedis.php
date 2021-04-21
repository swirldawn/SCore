<?php
namespace Score;
/**
 * Created by PhpStorm.
 * User: dawnlight
 * Date: 2018/7/31
 * Time: 下午5:29
 */
class DRedis{

    private static $dao = [];

    public static function getInstance($db = "redis"){

        if(isset(self::$dao[$db]) && !empty(self::$dao[$db])){
            return self::$dao[$db];
        }

        self::$dao[$db] = new Redis();

        self::$dao[$db]->connect(config("redis.host"), config("redis.port")); //连接Redis

        $pass = config("redis.pass");

        if($pass!=""){
            self::$dao[$db]->auth($pass); //密码验证
        }

        $database = config("redis.database");

        if($database!=""){
            self::$dao[$db]->select($database); //密码验证
        }

        return self::$dao[$db];
    }

    public static function __callStatic($name, $arguments)
    {
        $method =strtolower($name);

       return self::getInstance()->$method(...$arguments);

    }

}