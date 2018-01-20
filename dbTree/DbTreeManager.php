<?php

require_once(VENDOR_PATH . DS . 'dbTree' . DS . 'safemysql.class.php');
require_once(VENDOR_PATH . DS . 'dbTree' . DS . 'DbTree.class.php');
require_once(VENDOR_PATH . DS . 'dbTree' . DS . 'DbTreeExt.class.php');

class DbTreeManager
{
	private static $instance = null;
	
	protected $table 	= '';
	protected $id 		= 'id';
	protected $left 	= 'lft';
	protected $right 	= 'rgt';
	protected $level 	= 'level';
	
	public static function getInstance(Array $tree_params = null) {
		if(null === self::$instance) {
			self::$instance = new self();
		}

		if($tree_params) {
			foreach ($tree_params as $property => $value)
			{
				self::$instance->set($property, $value);
			}
		}
		
		return self::$instance->getDbTree();
	}
	
	protected function getDbTree()
	{
		if(!$this->get('table'))
		{
			throw new \Exception('Table option missing!');
		}
		if(!$this->get('id'))
		{
			throw new \Exception('`id` Field option missing!');
		}
		if(!$this->get('left'))
		{
			throw new \Exception('`left` Field option missing!');
		}
		if(!$this->get('right'))
		{
			throw new \Exception('`right` Field option missing!');
		}
		if(!$this->get('level'))
		{
			throw new \Exception('`level` Field option missing!');
		}
		
		
		$adapters = require(CONFIG_PATH . DS . 'autoload' . DS . 'defaults' . DS . 'adapters.db.php');

		$main_adapter = $adapters['main_adapter'];
		
		// Data base connect
		$dsn['user'] = $main_adapter['username'];
		$dsn['pass'] = $main_adapter['password'];
		$dsn['host'] = $main_adapter['host'];
		$dsn['db'] = $main_adapter['db'];
		$dsn['charset'] = 'utf8';
		$dsn['errmode'] = 'exception';
		
		if(!defined('DEBUG_MODE')) define('DEBUG_MODE', false);
		
		$db = new \SafeMySQL($dsn);
		
		$sql = 'SET NAMES utf8';
		$db->query($sql);
		
		$dbtree = new \DbTreeExt([
				'table' => $this->get('table'),
				'id' => $this->get('id'),
				'left' => $this->get('left'),
				'right' => $this->get('right'),
				'level' => $this->get('level'),
		], $db);
		
		return $dbtree;
	}
	
	public function set($property, $value)
	{
		if (!is_string($property) || empty($property) || !property_exists($this, $property)) {
			throw new \Exception(
				'Invalid argument: $property must be a non-empty string'
			);
		}
		
		$this->$property = $value;
	
		return $this;
	}
	public function get($property)
	{
		if (!is_string($property) || empty($property) || !property_exists($this, $property)) {
			throw new \Exception(
				'Invalid argument: $property must be a non-empty string'
			);
		}
		
		return $this->$property;
	}
}
