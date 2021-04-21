<?php
namespace Score;
/**
 * Created by PhpStorm.
 * User: dawnlight
 * Date: 2020/2/12
 * Time: 5:27 PM
 */
class Utils {

    public static function getBrowser() {
        global $_SERVER;
        $agent  = $_SERVER['HTTP_USER_AGENT'];
        $browser  = '';
        $browser_ver  = '';

        if (preg_match('/OmniWeb\/(v*)([^\s|;]+)/i', $agent, $regs)) {
            $browser  = 'OmniWeb';
            $browser_ver   = $regs[2];
        }

        if (preg_match('/Netscape([\d]*)\/([^\s]+)/i', $agent, $regs)) {
            $browser  = 'Netscape';
            $browser_ver   = $regs[2];
        }

        if (preg_match('/safari\/([^\s]+)/i', $agent, $regs)) {
            $browser  = 'Safari';
            $browser_ver   = $regs[1];
        }

        if (preg_match('/MSIE\s([^\s|;]+)/i', $agent, $regs)) {
            $browser  = 'Internet Explorer';
            $browser_ver   = $regs[1];
        }

        if (preg_match('/Opera[\s|\/]([^\s]+)/i', $agent, $regs)) {
            $browser  = 'Opera';
            $browser_ver   = $regs[1];
        }

        if (preg_match('/NetCaptor\s([^\s|;]+)/i', $agent, $regs)) {
            $browser  = '(Internet Explorer ' .$browser_ver. ') NetCaptor';
            $browser_ver   = $regs[1];
        }

        if (preg_match('/Maxthon/i', $agent, $regs)) {
            $browser  = '(Internet Explorer ' .$browser_ver. ') Maxthon';
            $browser_ver   = '';
        }
        if (preg_match('/360SE/i', $agent, $regs)) {
            $browser       = '(Internet Explorer ' .$browser_ver. ') 360SE';
            $browser_ver   = '';
        }
        if (preg_match('/SE 2.x/i', $agent, $regs)) {
            $browser       = '(Internet Explorer ' .$browser_ver. ') 搜狗';
            $browser_ver   = '';
        }

        if (preg_match('/FireFox\/([^\s]+)/i', $agent, $regs)) {
            $browser  = 'FireFox';
            $browser_ver   = $regs[1];
        }

        if (preg_match('/Lynx\/([^\s]+)/i', $agent, $regs)) {
            $browser  = 'Lynx';
            $browser_ver   = $regs[1];
        }

        if(preg_match('/Chrome\/([^\s]+)/i', $agent, $regs)){
            $browser  = 'Chrome';
            $browser_ver   = $regs[1];
        }


        if ($browser != '') {
            return ['browser'=>$browser,'version'=>$browser_ver];
        } else {
            return ['browser'=>'unknow browser','version'=>'unknow browser version'];
        }
    }

    public static function getBrowserPlat() {
        if(!isset($_SERVER['HTTP_USER_AGENT'])){
            return "Spider";
        }
        $agent  = $_SERVER['HTTP_USER_AGENT'];

        //
        if(preg_match('/spider/i', $agent, $regs)){
            return "Spider";
        }
        //
        if(preg_match('/baiduboxapp/i', $agent, $regs)){
            return "Spider";
        }

        //.net clr 渗透
        if(preg_match('/CLR/i', $agent, $regs)){
            return "Spider";
        }

        if(preg_match('/Googlebot/i', $agent, $regs)){
            return "Spider";
        }
        if (preg_match('/Mac/i', $agent, $regs)) {
            return "Mac";
        }

        if (preg_match('/Windows/i', $agent, $regs)) {
            return "Windows";
        }

        if (preg_match('/iPhone/i', $agent, $regs)) {
            return "iPhone";
        }

        if (preg_match('/iPad/i', $agent, $regs)) {
            return "iPad";
        }

        if (preg_match('/Android/i', $agent, $regs)) {
            return "Android";
        }

            return "Spider";
    }
}