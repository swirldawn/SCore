<?php

/**
 * sql辅助处理类
 * @author tongdesheng
 *
 */
namespace SCore\Db\Pdo;

class Sqlhelper {
	
	/**
	 * 构造查询参数
	 * @param array $params
	 */
	public static function buildSql($params) {
		$ret = array('sql' => '', 'params' => array());
		if(!empty($params)) {
			foreach ($params as $k => $param) {
				if(isset($param['v'])) {
					// array('v' => 1, 'op' => ''); 带运算符的形式
					if($param['op'] == 'in'){
						$ret['sql'] .= " and $k " . $param['op'] . '(';
						$ret['sql'] .= implode(',', $param['v']);
						$ret['sql'] .= ')';
					}else{
						$ret['sql'] .= " and $k " . ' '.$param['op'] .' '. ' :'  . $k;
						$ret['params'][$k] = $param['v'];
					}
					
				} else {
					//直接是值的方式，那么操作默认为等号
					$ret['sql'] .= " and $k=:$k";
					$ret['params'][$k] = $param;
						
				}
			}
		}
		return $ret;
	}
	
}