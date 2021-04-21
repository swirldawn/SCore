<?php
namespace Score;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
/**
 * Created by PhpStorm.
 * User: dawnlight
 * Date: 2019/1/2
 * Time: 3:16 PM
 */
class Log{

    public static function info(...$msg){
        // create a log channel
        $log = new Logger('name');
        $log->pushHandler(new StreamHandler(APPLICATION_PATH.config("log.path")."yaf_log.log", Logger::INFO));

        // add records to the log
       return  $log->info(json_encode($msg,JSON_UNESCAPED_UNICODE));
    }

    public static function warning(...$msg){
        // create a log channel
        $log = new Logger('name');
        $log->pushHandler(new StreamHandler(APPLICATION_PATH.config("log.path")."yaf_log.log", Logger::WARNING));

        // add records to the log
        return  $log->warning(json_encode($msg,JSON_UNESCAPED_UNICODE));
    }

    public static function error($msg,$data=[]){
        // create a log channel
        $log = new Logger('name');
        $log->pushHandler(new StreamHandler(APPLICATION_PATH.config("log.path")."yaf_log.log", Logger::ERROR));

        // add records to the log
        return  $log->error($msg,$data);
    }

    public static function getNginxLog(){
        $str = '117.136.63.154 - - [10/Jun/2020:10:27:05 +0800] "GET /apiuser/get_cashbook?jwt=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJwbGQiOnsic2l6ZSI6IjEwIiwicGFnZSI6IjEiLCJsYXN0dGltZSI6IjE5OTAtMDEtMDEgMDA6MDA6MDAiLCJ1c2VyX3Rva2VuIjoiZXlKaGJHY2lPaUpJVXpJMU5pSjkuZXlKbGVIQWlPakUxT1RReU1EWTBNRGNzSW5WcFpDSTZJak0wSW4wLldNN1dXOEtzX00wa19YbGx1QTdYVW84NEZ4T1FkWmxWcXoxYnRCdHU5Q2MifSwiaWF0IjoxNTkxNzU2MDI0fQ.cXHXzYsIcr5ol-H5sE6qSG_Zym9LZ63Xk4N9wxy7bAo HTTP/1.1" 200 2264 "-" "Mozilla/5.0 (Linux; Android 9; Mi Note 3 Build/PKQ1.181007.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/78.0.3904.96 Mobile Safari/537.36"';

        $ipPattern = '/(?:(?:2[0-4][0-9]\.)|(?:25[0-5]\.)|(?:1[0-9][0-9]\.)|(?:[1-9][0-9]\.)|(?:[0-9]\.)){3}(?:(?:2[0-5][0-5])|(?:25[0-5])|(?:1[0-9][0-9])|(?:[1-9][0-9])|(?:[0-9]))/';//需要转义/


        $statusPattren = '/\s([1-6]\d{2})\s/';
        preg_match($statusPattren,$str,$status);
        var_dump($status);

        preg_match($ipPattern,$str,$ip);

        $timePattern = '/\[(.+)\]\s/';

        preg_match($timePattern,$str,$time);
    }
}