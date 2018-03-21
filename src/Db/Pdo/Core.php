<?php

namespace SCore\Db\Pdo;

use \PDO as PDO;
use SCore\Server\ServerManager;
use \Exception;
use SCore\Cache;

class Core {
	
	/**
	 * 数据库对象
	 * @var PDO
	 */
	protected $_connection;
	
	/**
	 * 数据库名
	 * @var string
	 */
	protected $_dbName;
	
	/**
	 * 数据库连接用户名
	 * @var string
	 */
	protected $_userName;
	
	/**
	 * 数据库连接密码
	 * @var unknown
	 */
	protected $_password;

	protected $_dsn;
	
	/**
	 * 字符集
	 *
	 * @var string
	 */
	protected $_charset = 'utf8';
	
	/**
	 * 数据库类型
	 * @var unknown
	 */
	protected $_db_type = 'mysql';
	
	/**
	 * 数据库驱动参数
	 * @var array
	 */
	protected $_driverConfig = array(
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8',
			PDO::ATTR_PERSISTENT => false,
			PDO::ATTR_TIMEOUT => 30
	);
	
	protected $paramType = array(
			'integer' => PDO::PARAM_INT,
			'int' => PDO::PARAM_INT,
			'boolean' => PDO::PARAM_BOOL,
			'bool' => PDO::PARAM_BOOL,
			'string' => PDO::PARAM_STR,
			'null' => PDO::PARAM_NULL,
			'object' => PDO::PARAM_LOB,
			'float' => PDO::PARAM_STR,
			'double' => PDO::PARAM_STR
	);
	
	/**
	 * 常量强制列名为指定的大小写
	 * @var int
	*/
	protected $_caseFolding = PDO::CASE_LOWER;
	
	/**
	 * 是否使用缓存
	 * @var bool
	 */
	protected $_useCache = false;
	
	/**
	 * tag
	 * @var string
	 */
	private $_cacheTag = '';
	
	/**
	 * 缓存key
	 * @var string
	 */
	private $_cacheKey = '';
	
	/**
	 * 缓存时间
	 * @var integer
	 */
	private $_cacheExpire = 60;
	
	/**
	 * 缓存对象
	 * @var Cache_Memcached_Client
	 */
	private $_cacheObj = null;
	
	/**
	 * 连接key
	 * @var unknown
	 */
	private $_connectionKey = 'r';
	
	/**
	 * 选择服务器
	 * @param bool $isRead
	 */
	private function _getServerConfig($isRead = true) {
		$workMode = $isRead == true ? "read": "write";
		$serversConfig = ServerManager::getInstance()->loadServer('Db', $this->_dbName, $workMode);
		if (empty($serversConfig)) {
			throw new Exception('没有找到: ' . $this->dbname . ' 的配置文件');
		}
		return $serversConfig;
	}
	
	/**
	 * 测试连接
	 */
	public function testConnect() {
		try {
			$this->_connect();
			return true;
		}catch(Exception $e) {
			return false;
		}
	}
	
	/**
	 * 连接数据库
	 * @param bool $actMode 是否为写
	 */
	public function _connect($isRead = true)
	{
		$rwtag = $isRead === true ? 'r' : 'w';
		$this->_connectionKey = $this->_dbName . '_' . $rwtag;
		if (isset($this->_connection[$this->_connectionKey])) {
			return $this->_connection[$this->_connectionKey];
		}
		
		
		//oracle 设置长连接
		if($this->_db_type == 'oracle') {
			$this->_driverConfig = array(PDO::ATTR_PERSISTENT => true, PDO::ATTR_TIMEOUT => 30);
		}
		
		
		$dsn 	  = $this->_dsn;
		$userName = $this->_userName;
		$password = $this->_password;
		
		try {
			$this->_connection[$this->_connectionKey] = new PDO(
					$dsn,
					$userName,
					$password,
					$this->_driverConfig
			);
			// 强制列名为小写
			$this->_connection[$this->_connectionKey]->setAttribute(PDO::ATTR_CASE, $this->_caseFolding);
			// 设置为抛出异常
			$this->_connection[$this->_connectionKey]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
		} catch (\PDOException $e) {
			throw new Exception($e->getMessage(), $e->getCode(), $e);
		}
		return $this->_connection[$this->_connectionKey];
	}
	
	/**
	 * 回滚
	 * @return bool
	 */
	public function rollBack()
	{
		return $this->_connect(false)->rollBack();
	}
	
	/**
	 * 开启事务
	 * @return bool
	 */
	public function beginTransaction()
	{
		return $this->_connect(false)->beginTransaction();
	}
	
	/**
	 * 提交事务
	 * @return bool
	 */
	public function commit()
	{
		return $this->_connect(false)->commit();
	}
	
	/**
	 * 检查事务是否激活
	 * @return bool
	 */
	public function inTransaction()
	{
		return $this->_connect(false)->inTransaction();
	}
	
	/**
	 * 缓存
	 * @param bool $status
	 * @param String $key
	 * @param integer $expire
	 * @return
	 */
	public function cache($expire = 0)
	{
		
		$expire = intval($expire);
		if ($expire > 0) {
			$this->_useCache = true;
			$this->_cacheExpire = $expire;
		}
		return $this;
	}
	
	/**
	 * 设置缓存时间
	 *
	 * @param integer $expire 秒
	 * @return  Db_Pdo_Client
	 */
	public function expire($expire)
	{
		$expire = intval($expire);
		if ($expire > 0) {
			$this->_cacheExpire = $expire;
		}
		return $this;
	}
	
	/**
	 *
	 * @param string $tagName
	 * @return Db_Pdo_Client
	 */
	public function tag($tagName) {
		if(!empty($tagName)) {
			$this->_cacheTag = $tagName;
		}
		return $this;
	}
	
	/**
	 * 设置cache key
	 *
	 * @param String $key
	 * @return  Db_Pdo_Client
	 */
	public function key($key = null, $prefix = null)
	{
		if (!empty($key)) {
			$this->_cacheKey = $key;
		}
		return $this;
	}
	
	/**
	 * 实例化缓存对象
	 * @return Cache_Memcached_Client
	 */
	private function _dbCache(){
		if($this->_useCache == false) {
			return null;
		}
		if ($this->_cacheObj) {
			return $this->_cacheObj;
		}
		$prefix = $this->_dbName ? $this->_dbName : 'mysql.qteam';
		$this->_cacheObj = new Cache();
		$this->_cacheObj->setPrefix($prefix);
		return $this->_cacheObj;
	}
	/**
	 * 删除缓存
	 */
	private function _delCache() {
		if($this->_useCache == false) {
			return null;
		}
		if(empty($this->_cacheKey) && !empty($this->_cacheTag)) {
			//删除整个tag下所有的key
			$this->_cacheObj->deleteTag($this->_cacheTag);
		} else if(!empty($this->_cacheKey)){
			//通过指定key进行删除
			if(is_array($this->_cacheKey)) {
				foreach ($this->_cacheKey as $key) {
					if(empty($this->_cacheTag)) {
						$this->_cacheObj->delete($key);
					} else {
						$this->_cacheObj->tag($this->_cacheTag)->delete($key);
					}
				}
			} else if(is_string($this->_cacheKey)) {
				if(empty($this->_cacheTag)) {
					$this->_cacheObj->delete($this->_cacheKey);
				} else {
					$this->_cacheObj->tag($this->_cacheTag)->delete($this->_cacheKey);
				}
			}
		}
	}
	
	/**
	 * 预处理sql，主要是做替换操作，和一些防止sql注入的检测
	 * @param unknown $sql
	 * @param unknown $parameterMap
	 * @param string $replaceMap
	 */
	private function _dealSql($sql, $parameterMap, $replaceMap) {
		if(!empty($replaceMap)) {
			if(!is_array($replaceMap)) {
				throw new Exception('replaceMap'.'参数需为一个数组');
			}
			$newReplaceMap = array();
		     foreach ($replaceMap as $k => $v) {
			    if($this->_charset == 'us7ascii'){
			        $v = iconv('utf-8', 'gbk', $v);
			    }
				$newReplaceMap['#'. $k . '#'] = $v;
			}
			$sql = strtr($sql, $newReplaceMap);
		}
		return $sql;
	}
	
	/**
	 * 恢复默认设置
	 */
	protected function _resetParameter()
	{
		unset($this->_cacheKey);
		unset($this->_cacheTag);
		$this->_useCache = true;
	}
	
	/**
	 * 负责取数据
	 * @param string $sql
	 * @param array $parameterMap
	 * @param array $replaceMap
	 * @param string $fethType
	 */
	protected function _doFetch($sql, $parameterMap, $replaceMap, $fethType) {
		if($this->_useCache) {
			//读取缓存
			$this->_dbCache();
			if (empty($this->_cacheKey)) {
				$this->_cacheKey = md5($this->getSql($sql, $parameterMap, $replaceMap) . $fethType);
			}
			/** 判定是否获取Tag **/
			if (!empty($this->_cacheTag)) {
				$cacheVal = $this->_cacheObj->tag($this->_cacheTag)->get($this->_cacheKey);
			} else {
				$cacheVal = $this->_cacheObj->get($this->_cacheKey);
			}
			if (!empty($cacheVal)) {
				//TODO 是否需要恢复参数初始值
				return $cacheVal;
			}
		}
	
		$sql = $this->_dealSql($sql, $parameterMap, $replaceMap);
		$statement = $this->_connect(true)->prepare($sql);
		if(!empty($parameterMap)) {
			foreach ($parameterMap as $param => &$value){
				if (is_array($value) || is_object($value)) {
					throw new Exception ('bindParams: 参数不能是对象或数据');
				}
				if($this->_charset == 'us7ascii'){
				    $value = iconv('utf-8', 'gbk', $value);
				}
				$statement->bindValue($param, $value, $this->paramType[strtolower(gettype($value))]); //在此处如果这样写不能用bindParam，因为它的引用传值特性，会让最后一个参数覆盖前面的参数
			}
		}
		$statement->execute();
		switch ($fethType) {
			case 'fetchRow':
				$result = $statement->fetch(PDO::FETCH_ASSOC);
				if($this->_charset == 'us7ascii' && !empty($result)){
				    foreach ($result as $dk => $val) {
				        $result[$dk] =  $this->converCode($val);
				    }
				}
				break;
			case 'fetchOne':
				$result = $statement->fetchColumn(0);
				if($this->_charset == 'us7ascii'){
				    $result = $this->converCode($result);
				}
				break;
			case 'fetchAll':
				$result = $statement->fetchAll(PDO::FETCH_ASSOC);
		          if($this->_charset == 'us7ascii' && !empty($result)){
	 				foreach ($result as $dk => $val) {
	 					foreach ($val as $k => $v) {
	 						$result[$dk][$k] = $this->converCode($v);
	 					}
	 				}
				}
				break;
			case 'fetchColArr':
				$data = $statement->fetchAll(PDO::FETCH_ASSOC);
				$result = array();
				if(!empty($data)) {
					foreach ($data as $val) {
						if($this->_charset == 'us7ascii'){
							$result[] = $this->converCode(current($val));;
						}else{
							$result[] = current($val);
						}
					}
				}
				break;
			case 'fetchAssoc':
				$data = $statement->fetchAll(PDO::FETCH_ASSOC);
		      $result = array();
				foreach ($data as $val) {
					$tmp = array_values($val);
					if($this->_charset == 'us7ascii'){
						foreach ($val as $k => $v) {
							$val[$k]  = $this->converCode($v);
						}
					}
					$result[$tmp[0]] = $val;
				}
				break;
					
		}
		//取到了数据，设置缓存,供下次取用
		if ($this->_useCache === true) {
			if (!empty($this->_cacheTag)) {
				$this->_cacheObj->tag($this->_cacheTag)->set($this->_cacheKey, $result, $this->_cacheExpire);
			} else {
				$this->_cacheObj->set($this->_cacheKey, $result, $this->_cacheExpire);
			}
		}
		$this->_resetParameter();
		return $result;
	}
	/**
	 * 转换编码
	 * @param unknown $v
	 * @return string
	 */
	protected function converCode($v){
	    if(!empty($v)){
	        if(is_resource($v)){
	            $v = iconv('gbk', 'utf-8', stream_get_contents($v));
	        }else if(is_string($v)){
	            $v = iconv('gbk', 'utf-8', $v);
	        }
	    }
	    return $v;
	}
	/**
	 * 增，删，改等变更数据的操作调用此函数
	 * @param $sqlId
	 * @param array $parameterMap
	 * @param null $replaceMap
	 * @return Db_Mysql_Result
	 */
	protected function _doChange($sql, $parameterMap, $replaceMap)
	{
		if(empty($sql)) {
			throw new Exception('sql语句不能为空', 401);
		}
		$sql = $this->_dealSql($sql, $parameterMap, $replaceMap);
		$this->_dbCache();  //初始化缓存对象
		$statement = $this->_connect(false)->prepare($sql);
		if(!empty($parameterMap)) {
			foreach ($parameterMap as $param => &$value){
				if (is_array($value) || is_object($value)) {
					throw new Exception ('bindParams: 参数不能是对象或数据');
				}
				if($this->_charset == 'us7ascii'){
				    $value = iconv('utf-8', 'gbk', $value);
				}
				//兼容oracle by zhaokui2015-12-15
				if(is_string($value) && strlen($value) >= 1000){
					$statement->bindParam($param, $value, $this->paramType[strtolower(gettype($value))], strlen($value));
				}else{
					$statement->bindValue($param, $value, $this->paramType[strtolower(gettype($value))]);
				}
			}
		}
		$statement->execute();
		$this->_delCache();
		$this->_resetParameter();
		return new Result($this->_connection[$this->_connectionKey], $statement, $this->_db_type);
	}
	
	/**
	 * 存储过程,注意：oracle下通过，mysql下未使用过
	 */
	protected function _doExec($sql, $parameterMap, $replaceMap)
	{
		if(empty($sql)) {
			throw new Exception('sql语句不能为空', 401);
		}
		$sql = $this->_dealSql($sql, $parameterMap, $replaceMap);
		$this->_dbCache();  //初始化缓存对象
		$statement = $this->_connect(false)->prepare($sql);
		$procedure_return_value = 0;//存储过程的返回值 by zhaokui 2015-12-15
		if(!empty($parameterMap)) {
			foreach ($parameterMap as $param => &$value){
				if (is_array($value) || is_object($value)) {
					throw new Exception ('bindParams: 参数不能是对象或数据');
				}
				if($this->_charset == 'us7ascii'){
					$value = iconv('utf-8', 'gbk', $value);
				}
				//存储过程的返回值 by zhaokui 2015-12-15
				if($param == 'procedure_return_value'){
					$statement->bindParam($param, $procedure_return_value, $this->paramType[strtolower(gettype($value))], is_numeric($value)?20:2000);
				}else{
					$statement->bindValue($param, $value, $this->paramType[strtolower(gettype($value))]);
				}
			}
		}
		 
		$statement->execute();
		$this->_delCache();
		$this->_resetParameter();
// 		$result = new Q_Db_Pdo_Result($this->_connection[$this->_connectionKey], $statement, $this->_db_type);
		$result = new Result($this->_connection[$this->_connectionKey], $statement, $this->_db_type);
		//存储过程的返回值 by zhaokui 2015-12-15
		if($procedure_return_value > 0){
			$result->setProcedureReturnValue($procedure_return_value);
		}
		return $result;
	}
	
	/**
	 * 获取sql
	 * @param string $sql
	 * @param string $parameterMap
	 * @param string $replaceMap
	 * @return Ambigous <string, mixed>
	 */
	public function getSql($sql, $parameterMap = array(), $replaceMap = null)
	{
		$sql = $this->_dealSql($sql, $parameterMap, $replaceMap);
		if (strstr($sql, ':')) {
			$matches_s = array();
			foreach ($parameterMap as $key => $val) {
				if (is_string($val)) {
					$val = "'{$val}'";
				}
				$matches_s[':' . $key] = $val;
			}
			$asSql = strtr($sql, $matches_s);
		} else {
			$asSql = $sql;
			foreach ($parameterMap as $val) {
				$strPos = strpos($asSql, '?');
				if (is_string($val)) {
					$val = "'{$val}'";
				}
				$asSql = substr_replace($asSql, $val, $strPos, 1);
			}
		}
		return $asSql;
	}
	
}