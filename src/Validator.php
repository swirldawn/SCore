<?php
namespace SCore;
/**
 * 验证类
 */
class Validator {

    public static $_errormsg = [
            "required"      =>"不能为空",
            "chinese"       =>"不是中文",
            "number"        =>"不是数字",
            "integer"       =>"不是int型",
            "plusinteger"   =>"不在int型范围",
            "double"        =>"不是浮点数",
            "plusdouble"    =>"不在浮点数范围",
            "en"            =>"不是英文",
            "username"      =>"用户名不符合规范",
            "mobile"        =>"手机号格式不对",
            "phone"         =>"手机号格式不对",
            "tel"           =>"电话号格式不对",
            "email"         =>"邮箱格式不对",
            "url"           =>"网址格式不对",
            "ip"            =>"ip格式不对",
            "qq"            =>"qq号格式不对",
            "currency"      =>"不是货币格式",
            "zip"           =>"不是zip文件",
            "exists"        =>"数据库里不存在",
            "unique"        =>"数据库里已经存在",
            "length"        =>"长度不对",
            "between"        =>"长度不对",
    ];
    /**
     * @$role array()
     * @param 汇总的验证方法
     * $role=[
     *          ["game_id"=>"required|integer|exists:game,id"],
     *       ];
     */
    public static function validate($params,$roles,$errorMsg=null){
        $error=[];
        $data=['code'=>200];

        foreach($roles as $k=>$v){
            $role = explode("|",$v);
            //如果val是空,验证类型不是required则不判断
            if(in_array("required",$role) && !isset($params[$k])){
                    $error[$k] = "{$k}必须传参";
                    continue;
            }
            if(isset($params[$k])){
                foreach($role as $item){
                    if(!self::validateOne($params[$k],$item)){
                        if($errorMsg != null && isset($errorMsg[$k])){
                            $error[$k] = $errorMsg[$k];
                        }else{//定义错误信息
                            $roleKey = explode(":",$item);
                            $error[$k] = $k.self::$_errormsg[$roleKey[0]];
                        }
                    }
                }
            }
        }

        if(count($error)>0){
            $data['code']=400;
            $data['error']=$error;
        }
        return $data;
    }
    /**
     * @param $val
     * @return b
     * url http://www.pintuer.com/javascript.html#form-vars
     */
    public static function validateOne($pintu,$type){

        switch($type){
                case "required":return preg_match('/[^(^\s*)|(\s*$)]/',$pintu);break;
                case "chinese":return preg_match('/^[\x7f-\xff]+$/',$pintu);break;
                case "number":return preg_match('/^\d+$/',$pintu);break;
                case "integer":return preg_match('/^[-\+]?\d+$/',$pintu);break;
                case "plusinteger":return preg_match('/^[+]?\d+$/',$pintu);break;
                case "double":return preg_match('/^[-\+]?\d+(\.\d+)?$/',$pintu);break;
                case "plusdouble":return preg_match('/^[+]?\d+(\.\d+)?$/',$pintu);break;
                case "en":return preg_match('/^[A-Za-z]+$/',$pintu);break;
                case "username":return preg_match('/^[a-z]\w{3,}$/i',$pintu);break;
                case "mobile":return preg_match('/^((\(\d{3}\))|(\d{3}\-))?13[0-9]\d{8}?$|15[89]\d{8}?$|170\d{8}?$|147\d{8}?$/',$pintu);break;
                case "phone":return preg_match('/^((\(\d{2,3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}(\-\d{1,4})?$/',$pintu);break;
                case "tel":return preg_match('/^((\(\d{3}\))|(\d{3}\-))?13[0-9]\d{8}?$|15[89]\d{8}?$|170\d{8}?$|147\d{8}?$/',$pintu) || preg_match('/^((\(\d{2,3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}(\-\d{1,4})?$/',$pintu);break;
                case "email":return preg_match('/^[^@]+@[^@]+\.[^@]+$/',$pintu);break;
                case "url":return preg_match('/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/',$pintu);break;
                case "ip":return preg_match('/^[\d\.]{7,15}$/',$pintu);break;
                case "qq":return preg_match('/^[1-9]\d{4,10}$/',$pintu);break;
                case "currency":return preg_match('/^\d+(\.\d+)?$/',$pintu);break;
                case "zip":return preg_match('/^[1-9]\d{5}$/',$pintu);break;
            default:
                $role= explode (':',$type);
                switch($role[0]){
                    case "length":
                        $length=mb_strlen($pintu,'UTF8');
                        $num=explode (',',$role[1]);
                            if(isset($num[1])){
                                return $length>=$num[0]&&$length<=$num[1]?true:false;
                            }
                        return $length>=$num[0];
                        break;
                    case "between":
                        $length=mb_strlen($pintu,'UTF8');
                        $num=explode (',',$role[1]);
                            if(isset($num[1])){
                                return $length>=$num[0]&&$length<=$num[1]?true:false;
                            }
                        return $length>=$num[0];
                        break;
                    case "exists":
                        $table = explode (',',$role[1]);
                        $re = DB::table($table[0])->where($table[1],$pintu)->first();
                        if(empty($re)){
                            return false;
                        }
                        return true;
                        break;
                    case "unique":
                        $table = explode (',',$role[1]);
                        $re = DB::table($table[0])->where($table[1],$pintu)->first();
                        if(!empty($re)){
                            return false;
                        }
                        return true;
                        break;
                }


			}
    }
    /*
          函数名称：isNumber
          简要描述：检查输入的是否为数字
          输入：string
          输出：boolean
     */
 
    public static function isNumber($val) {
        if (ereg("^[0-9]+$", $val))
            return TRUE;
        return FALSE;
    }
 
    /*
     * 函数名称：isPhone
     * 简要描述：检查输入的是否为电话
     * 输入：string
     * 输出：boolean
     */
 
    public static function isPhone($val) {
        //eg: xxx-xxxxxxxx-xxx | xxxx-xxxxxxx-xxx ...
        if (ereg("^((0\d{2,3})-)(\d{7,8})(-(\d{3,}))?$", $val))
            return TRUE;
        return FALSE;
    }
 
    /*
     * 函数名称：isMobile
     * 简要描述：检查输入的是否为手机号
     * 输入：string
     * 输出：boolean
     */
 
    public static function isMobile($val) {
        //该表达式可以验证那些不小心把连接符“-”写出“－”的或者下划线“_”的等等
        if (ereg("(^(\d{2,4}[-_－—]?)?\d{3,8}([-_－—]?\d{3,8})?([-_－—]?\d{1,7})?$)|(^0?1[35]\d{9}$)", $val))
            return TRUE;
        return FALSE;
    }
 
    /*
     * 函数名称：isPostcode
     * 简要描述：检查输入的是否为邮编
     * 输入：string
     * 输出：boolean
     */
 
    public static function isPostcode($val) {
        if (ereg("^[0-9]{4,6}$", $val))
            return TRUE;
        return FALSE;
    }
 
    /*
     * 函数名称：isEmail
     * 简要描述：邮箱地址合法性检查
     * 输入：string
     * 输出：boolean
     */
 
    public static function isEmail($val, $domain = "") {
        if (!$domain) {
            if (preg_match("/^[a-z0-9-_.]+@[\da-z][\.\w-]+\.[a-z]{2,4}$/i", $val)) {
                return TRUE;
            } else
                return FALSE;
        }
        else {
            if (preg_match("/^[a-z0-9-_.]+@" . $domain . "$/i", $val)) {
                return TRUE;
            } else
                return FALSE;
        }
    }
 
//end func
 
    /*
     * 函数名称：isName
     * 简要描述：姓名昵称合法性检查，只能输入中文英文
     * 输入：string
     * 输出：boolean
     */
 
    public static function isName($val) {
        if (preg_match("/^[\x80-\xffa-zA-Z0-9]{3,60}$/", $val)) {//2008-7-24
            return TRUE;
        }
        return FALSE;
    }
 
//end func
 
    /*
     * 函数名称:isDomain($Domain)
     * 简要描述:检查一个（英文）域名是否合法
     * 输入:string 域名
     * 输出:boolean
     */
 
    public static function isDomain($Domain) {
        if (!eregi("^[0-9a-z]+[0-9a-z\.-]+[0-9a-z]+$", $Domain)) {
            return FALSE;
        }
        if (!eregi("\.", $Domain)) {
            return FALSE;
        }
 
        if (eregi("\-\.", $Domain) or eregi("\-\-", $Domain) or eregi("\.\.", $Domain) or eregi("\.\-", $Domain)) {
            return FALSE;
        }
 
        $aDomain = explode(".", $Domain);
        if (!eregi("[a-zA-Z]", $aDomain[count($aDomain) - 1])) {
            return FALSE;
        }
 
        if (strlen($aDomain[0]) > 63 || strlen($aDomain[0]) < 1) {
            return FALSE;
        }
        return TRUE;
    }
 
    /*
     * 函数名称:isNumberLength($theelement, $min, $max)
     * 简要描述:检查字符串长度是否符合要求
     * 输入:mixed (字符串，最小长度，最大长度)
     * 输出:boolean
     */
 
    public static function isNumLength($val, $min, $max) {
        $theelement = trim($val);
        if (ereg("^[0-9]{" . $min . "," . $max . "}$", $val))
            return TRUE;
        return FALSE;
    }
 
    /*
     * 函数名称:isNumberLength($theelement, $min, $max)
     * 简要描述:检查字符串长度是否符合要求
     * 输入:mixed (字符串，最小长度，最大长度)
     * 输出:boolean
     */
 
    public static function isEngLength($val, $min, $max) {
        $theelement = trim($val);
        if (ereg("^[a-zA-Z]{" . $min . "," . $max . "}$", $val))
            return TRUE;
        return FALSE;
    }
 
    /*
     * 函数名称：isEnglish
     * 简要描述：检查输入是否为英文
     * 输入：string
     * 输出：boolean
     */
 
    public static function isEnglish($theelement) {
        if (ereg("[\x80-\xff].", $theelement)) {
            return FALSE;
        }
        return TRUE;
    }
 
    /*
     * 函数名称：isChinese
     * 简要描述：检查是否输入为汉字
     * 输入：string
     * 输出：boolean
     */
 
    public static function isChinese($sInBuf) {
        $iLen = strlen($sInBuf);
        for ($i = 0; $i < $iLen; $i++) {
            if (ord($sInBuf{$i}) >= 0x80) {
                if ((ord($sInBuf{$i}) >= 0x81 && ord($sInBuf{$i}) <= 0xFE) && ((ord($sInBuf{$i + 1}) >= 0x40 && ord($sInBuf{$i + 1}) < 0x7E) || (ord($sInBuf{$i + 1}) > 0x7E && ord($sInBuf{$i + 1}) <= 0xFE))) {
                    if (ord($sInBuf{$i}) > 0xA0 && ord($sInBuf{$i}) < 0xAA) {
//有中文标点
                        return FALSE;
                    }
                } else {
//有日文或其它文字
                    return FALSE;
                }
                $i++;
            } else {
                return FALSE;
            }
        }
        return TRUE;
    }
 
    /*
     * 函数名称：isDate
     * 简要描述：检查日期是否符合0000-00-00
     * 输入：string
     * 输出：boolean
     */
 
    public static function isDate($sDate) {
        if (ereg("^[0-9]{4}\-[][0-9]{2}\-[0-9]{2}$", $sDate)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
 
    /*
     * 函数名称：isTime
     * 简要描述：检查日期是否符合0000-00-00 00:00:00
     * 输入：string
     * 输出：boolean
     */
 
    public static function isTime($sTime) {
        if (ereg("^[0-9]{4}\-[][0-9]{2}\-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$", $sTime)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
 
    /*
     * 函数名称:isMoney($val)
     * 简要描述:检查输入值是否为合法人民币格式
     * 输入:string
     * 输出:boolean
     */
 
    public static function isMoney($val) {
        if (ereg("^[0-9]{1,}$", $val))
            return TRUE;
        if (ereg("^[0-9]{1,}\.[0-9]{1,2}$", $val))
            return TRUE;
        return FALSE;
    }
 
    /*
     * 函数名称:isIp($val)
     * 简要描述:检查输入IP是否符合要求
     * 输入:string
     * 输出:boolean
     */
 
    public static function isIp($val) {
        return (bool) ip2long($val);
    }
 
}