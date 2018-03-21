<?php

namespace SCore\Db\Pdo;

class Result {
	
	/**
	 * @var PDO
	 */
	private $_pdo;
	
	private $_db_type;
	
	/**
	 * @var PDOStatement
	 */
	private $_statement;
	
	/*存储过程的返回值 by zhaokui 2015-12-15*/
	private $_procedure_return_value;
	
	public function __construct(&$_pdo, &$_statement, $db_type)
	{
		$this->_pdo = $_pdo;
		$this->_statement = $_statement;
		$this->_db_type = $db_type;
	}
	
	/**
	 * 返回插入ID
	 * @return integer
	 */
	public function lastInsertId($seqName='')
	{
		if($this->_db_type == 'mysql') {
			return $this->_pdo->lastInsertId();
		} else if($this->_db_type == 'oracle') {
			$stmt  = $this->_pdo->prepare("select {$seqName}.currval as lastId from dual");
			$stmt->execute();
			return $stmt->fetchColumn(0);
		}
		throw new \Exception('获取自增ID失败');
	}
	
	/**
	 * 返回执行sql的状态
	 * @return bool
	 */
	public function status()
	{
		return true;
	}
	
	/**
	 * 返回错误码
	 * @return String
	 */
	public function errorCode()
	{
		return $this->_pdo->errorCode();
	}
	
	/**
	 * 返回错误信息
	 * @return Array
	 */
	public function errorInfo()
	{
		return $this->_pdo->errorInfo();
	}
	
	/**
	 * 返回插入影响的行数
	 * @return integer
	 */
	public function rowCount()
	{
		return $this->_statement->rowCount();
	}
	
	/**
	 * @return bool
	 */
	public function nextRowset()
	{
		return $this->_statement->nextRowset();
	}
	
	public function setProcedureReturnValue($value){
		$this->_procedure_return_value = $value;
	}
	
	public function getProcedureReturnValue(){
		return $this->_procedure_return_value;
	}
}