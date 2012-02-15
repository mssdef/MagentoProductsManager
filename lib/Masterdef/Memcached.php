<?php

define('MDF_MEMCACHE_TSHORT', 60 * 3);
define('MDF_MEMCACHE_TMID', 60 * 15);
define('MDF_MEMCACHE_TLONG', 60 * 60 * 1);
define('MDF_MEMCACHE_TLONGXL', 60 * 60 * 6);
define('MDF_MEMCACHE_TLONGXXL', 60 * 60 * 30);

include_once dirname(__FILE__) . '/../../application/configs/config.php';

class Masterdef_Memcached
{
    protected static $_instance; static function I() { if (self::$_instance === NULL) { self::$_instance = new self(); } return self::$_instance; } 

	var $memcached;

	function __construct() {
		if (MDF_MEMCACHE_HOST) {
			$this->memcached	= new Memcache();

			$this->memcached->connect(MDF_MEMCACHE_HOST, MDF_MEMCACHE_PORT);
		}
		//$this->flush();
	}

	function get($key) {
		if ($this->memcached) return $this->memcached->get($key);
		else return null;
	}

	function set($key, $value, $lifetime = MDF_MEMCACHE_TSHORT) {
		if ($this->memcached) $this->memcached->set($key, $value, 0, $lifetime);
		else ;
	}


	function flush() {
		$this->memcached->flush();
	}
}
