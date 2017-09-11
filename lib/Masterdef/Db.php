<?php

class Masterdef_Db {
	protected static $_instance; 
  static function I() { if (self::$_instance === NULL) { self::$_instance = new self(); } return self::$_instance; } 

	protected $_cache_live = 60;
  protected $_zend_db;

	function __construct() { }

  /**
   *  get zend database object
   **/
	public function zend_db() {
    if (!$this->_zend_db) {
        $config = Zend_Registry::get('config');
        $params = $config->resources->db->params;
        $this->_zend_db = Zend_Db::factory($config->resources->db->adapter, $params);
    }

    return $this->_zend_db;
	}

  /**
   *  pack row extra
   **/
  public function pack_extra($row) {
    if (@$row['_extra']) {
      if (!@unserialize($row['_extra'])) {
        $row['_extra']  = serialize($row['_extra']);
      }
    }

    return $row;
  }

  /**
   *  unpack row extra
   **/
  public function unpack_extra($row) {
    if (@$row['_extra']) {
      if (unserialize($row['_extra'])) {
        $row['_extra']  = unserialize($row['_extra']);
      }
    }

    return $row;
  }

}

