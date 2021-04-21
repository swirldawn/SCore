<?php
namespace Score;
/**
 * Created by PhpStorm.
 * User: dawnlight
 * Date: 2018/7/23
 * Time: 下午5:53
 */
class DB {

    protected  $table = "";

    protected  $where = [];

    protected  $whereRaw = "";

    protected  $limit = [];

    protected  $order = [];

    protected  $column = "*";

    protected  $error = false;

    protected  $daoObject = null;

    protected static $dao = [];

    public static function getDao($db="db"){

        if(isset(DB::$dao[$db]) && !empty(DB::$dao[$db])){
            return DB::$dao[$db];
        }

        DB::$dao[$db] = new Medoo([
            'database_type' => get_config("{$db}.database_type"),
            'database_name' => get_config("{$db}.database_name"),
            'server'        => get_config("{$db}.server"),
            'username'      => get_config("{$db}.username"),
            'password'      => get_config("{$db}.password"),
            'option' => [
                PDO::ATTR_STRINGIFY_FETCHES =>false,//禁止把结果全部改成字符串
                PDO::ATTR_EMULATE_PREPARES =>false
            ]
        ]);

        return DB::$dao[$db];
    }

    /**
     * dawnlight 2021/3/2 2:57 PM
     * @param $method
     * @param $arg_array
     * @return mixed
     * @throws Exception
     * 使用这种魔术方法是因为 有时候会需要获取不同的数据库DAO
     * 还没想到更好的方法执行这种
     */
    public static function __callStatic( $method, $arg_array ){

        $db  = self::getDao();

        switch ($method){
            case "select":
                $query = $db->query(...$arg_array);
                self::error($db);
                return $query->fetchAll(PDO::FETCH_ASSOC);
                break;
            case "exec":
                $result = $db->exec(...$arg_array);
                self::error($db);
                return $result;
                break;
        }
    }

    public function __call( $method, $arg_array ){
        $db = $this->daoObject;
        switch ($method){
            case "select":
                $query = $db->query(...$arg_array);
                $this->error($db);
                return $query->fetchAll(PDO::FETCH_ASSOC);
                break;
            case "exec":
                $result = $db->exec(...$arg_array);
                $this->error($db);
                return $result;
                break;
            case "table":
                $this->table = $arg_array[0];
                return $this;
                break;
        }
    }


    public static function connect($connect){

        $db = new DB();

        $db->daoObject = self::getDao($connect);

        return $db;
    }

    public function setTable($table_name){

        $this->table = $table_name;

        return $this;
    }

    public static function table($table_name,$isRealTable=false){

        $db = new DB();

        $db->daoObject = self::getDao();

        if($isRealTable){
            $db->table = $table_name;
        }else{
            $db->table = TABLE_PREFIX.$table_name;
        }


        return $db;
    }

    public function paginate($size = 10,$page=1){

        $getPage = isset($_GET['page'])?$_GET['page']:null;
        $getSize = isset($_GET['size'])?$_GET['size']:null;

        $getPage&&$page = $getPage;
        $getSize&&$size = $getSize;

        $re = [];
        $count = $this->count();
        $lastPage   = ceil($count/$size);
        $offset     = abs($page - 1) * $size;

        $re["total"]        = $count;
        $re["per_page"]     = $size;
        $re["last_page"]    = $lastPage;
        $re["current_page"] = $page;
        $re["offset"]       = $offset;

        $this->where['LIMIT'] =  [$offset, $size];

        if(count($this->order)){
            $this->where['ORDER'] =  $this->order;
        }
        $re["data"] = $this->daoObject->select($this->table, $this->column ,$this->where);

        $this->error($this->daoObject);

        return $re;
    }

    public function count(){

        $count = $this->daoObject->count($this->table,$this->where);
        $this->error($this->daoObject);
        return $count;
    }

    public function max($col){
        $max = $this->daoObject->max($this->table,$col,$this->where);
        $this->error($this->daoObject);
        return $max;
    }

    public function min($col){
        $min = $this->daoObject->max($this->table,$col,$this->where);
        $this->error($this->daoObject);
        return $min;
    }

    public function columns($columns){

        $this->column = $columns;

        return $this;
    }

    public function insertADS($data){
        $col = "";
        $value = "";
        foreach($data as $key=>$val){
            $col .="`{$key}`,";
            $value .= gettype($val)=="integer"?"{$val}":"'{$val}'";
            $value .= ",";
        }
        $col =rtrim($col,',');
        $value =rtrim($value,',');

        $sql = "insert into `{$this->table}` ({$col}) values($value)";
        return self::exec($sql);
    }

    public function where($columns,$claim,$where = null){

        if($where === null){
            $this->where[$columns] = $claim;
        }else{
            if($claim == "<>"){
                $claim = "!";
            }
            $this->where[$columns."[{$claim}]"]= $where;
        }
        return $this;
    }

    public function whereRaw($where){

        $this->where .= " and ".$where;

        return $this;
    }

    public  function limit($skip,$take=null){

        if($take == null){
            $this->limit = $skip;
        }else{
            $this->limit = [$skip,$take];
        }
        return $this;
    }

    public function orderBy($columns,$order="ASC"){

        $this->order[$columns] = strtoupper($order);

        return $this;
    }
    /*
     * 废弃
     */
    public function getSql($type=""){

        if($type == "del"){
            $sql = "delete from ".$this->table." where  1=1 ".$this->where.$this->limit;
        }else if($type == "count"){
            $sql = "select count(1) as count from ".$this->table." where  1=1 ".$this->where;
        }else{
            $sql = "select ".$this->column." from ".$this->table." where  1=1 ".$this->where.$this->order.$this->limit;
        }

        return $sql;
    }

    public function get(){

        if(!empty($this->limit)){
            $this->where['LIMIT'] = $this->limit;
        }
        if(count($this->order)){
            $this->where['ORDER'] = $this->order;
        }

        $data = $this->daoObject->select($this->table, $this->column , $this->where);

        $this->error($this->daoObject);

        return $data;

    }

    public function first(){

        $data = $this->daoObject->get($this->table, $this->column , array_merge($this->where,["LIMIT" => 1]));

        $this->error($this->daoObject);

        return $data;

    }

    public function insert($data){

        $db = $this->daoObject;

        $db->insert($this->table,$data);

        $this->error($this->daoObject);

        return $db->id();
    }

    public function update($data,$where=[]){

        $db = $this->daoObject;

        $re = $db->update($this->table,$data,array_merge($this->where,$where));

        $this->error($this->daoObject);

        return $re->rowCount();
    }

    public function delete(){

        if(empty($this->where)){
            return false;
        }

        $re = $this->daoObject->delete($this->table,$this->where);

        $this->error($this->daoObject);

        return $re;
    }

    public  function exec1($sql){
        $db = self::getDao();

        return $db->exec($sql);
    }

    public  function select1($sql,$map = []){

        $db = self::getDao();

        $data = $db->query($sql,$map)->fetchAll(PDO::FETCH_ASSOC);

        return $data;
    }

    public static function error($db){

        $error = $db->pdo->errorInfo();

        if($error[0]!="00000"){
            throw new Exception(implode("-",$error),500);
        }

        return false;
    }





}