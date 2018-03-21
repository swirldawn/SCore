<?php

namespace SCore\Db\Pdo;

class PdoClient extends Core{
	
	/**
	 *
	 * @param string $dbName
	 */
	public function __construct($dbName, $username='', $password='',$dsn="",$dbType="") {
		$this->_dbName = $dbName;
		$this->_userName = $username;
		$this->_password = $password;
		$this->_dsn = $dsn;
		if($dbType != ""){
			$this->_db_type = $dbType;
		}
		
	}
	
	/**
	 * 获取数据
	 * @param string $sql
	 * @param array $parameterMap
	 * @param array $replaceMap
	 */
	public function fetchRow($sql, $parameterMap = array(), $replaceMap = null)
	{
		return $this->_doFetch($sql, $parameterMap, $replaceMap, __FUNCTION__);
	}
	
	/**
	 * 获取第一列的值
	 * @param string $sql
	 * @param array $parameterMap
	 * @param array $replaceMap
	 */
	public function fetchOne($sql, $parameterMap = array(), $replaceMap = null) {
		return $this->_doFetch($sql, $parameterMap, $replaceMap, __FUNCTION__);
	}
	
	/**
	 * 获取所有结果数组
	 * @param string $sql
	 * @param array $parameterMap
	 * @param array $replaceMap
	 */
	public function fetchAll($sql, $parameterMap = array(), $replaceMap = null) {
		return $this->_doFetch($sql, $parameterMap, $replaceMap, __FUNCTION__);
	}
	
	/**
	 * 获取关联数组形式的结果
	 * @param string $sql
	 * @param array $parameterMap
	 * @param array $replaceMap
	 */
	public function fetchAssoc($sql, $parameterMap = array(), $replaceMap = null) {
		return $this->_doFetch($sql, $parameterMap, $replaceMap, __FUNCTION__);
	}
	
	/**
	 * 返回结果的第一列数组
	 * @param array $sql
	 * @param array $parameterMap
	 * @param string $replaceMap
	 * @return Ambigous <unknown, multitype:>
	 */
	public function fetchColArr($sql, $parameterMap = array(), $replaceMap = null) {
		return $this->_doFetch($sql, $parameterMap, $replaceMap, __FUNCTION__);
	}
	
	/**
	 * 插入数据
	 * @param string $sql
	 * @param array $parameterMap
	 * @param array $replaceMap
	 * @return \QCore\Db\Pdo\Result
	 */
	public function insert($sql, $parameterMap = array(), $replaceMap = null) {
		return $this->_doChange($sql, $parameterMap, $replaceMap);
	}
	
	/**
	 *  更新数据
	 * @param string $sql
	 * @param array $parameterMap
	 * @param array $replaceMap
	 */
	public function update($sql, $parameterMap = array(), $replaceMap = null) {
		return $this->_doChange($sql, $parameterMap, $replaceMap);
	}
	
	/**
	 * 删除
	 * @param string $sql
	 * @param array $parameterMap
	 * @param array $replaceMap
	 */
	public function delete($sql, $parameterMap = array(), $replaceMap = null) {
		return $this->_doChange($sql, $parameterMap, $replaceMap);
	}
	
	/**
	 * 存储过程
	 * @param string $sql
	 * @param string $parameterMap
	 * @param string $replaceMap
	 * @return Ambigous <string, mixed>
	 */
	public function execSql($sql, $parameterMap = array(), $replaceMap = null) {
		return $this->_doExec($sql, $parameterMap, $replaceMap);
	}
}