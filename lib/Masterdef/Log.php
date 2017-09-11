<?php

/**
 *  log messages
 **/
class Masterdef_Log {
    /** singleton **/
	protected static $_instance; 
    static function I() { if (self::$_instance === NULL) { self::$_instance = new self(); } return self::$_instance; } 

	protected $_log_writer;
	protected $_log_file	= 'tmp/var.log';
    protected $_log_visitors  = 'public/tmp/visitors.txt';
    protected $_firebug_instance;

	function __construct() {
	}

  public function base_path() {
    return realpath(dirname(__FILE__) . '/../../');
  }

  /**
   *  write debug message to firebug console
   **/
  public function firebug() {
  }

  /**
   * get log file path
   **/
  public function get_log_file_path() {
    $log_filename = $this->base_path() . "/" . $this->_log_file;
    return $log_filename;
  }

    /**
     *  Error log
     **/
	public function log() 
    {
        ob_start();
        var_dump(func_get_args());
        $dump_str = ob_get_contents();
        ob_end_clean();

        $log_filename = $this->get_log_file_path();

        if (is_writable($log_filename))
        {
            error_log($dump_str, 3, $log_filename);
        } else
        {
            error_log($dump_str);
        }
	}

  public function log_visitors() {
    global $_SERVER;

    $referer  = @$_SERVER['HTTP_REFERER'];
    $agent    = @$_SERVER['HTTP_USER_AGENT'] ? $_SERVER['HTTP_USER_AGENT'] : 'unknown';

    $msg  = "{$_SERVER['REMOTE_ADDR']}\n" .
      "{$agent}\n" .
      "{$referer}\n" .
      "*\n";

    error_log($msg, 3, $this->base_path() . $this->_log_visitors);
  }

  public function backtrace($call_id = '') {
    $r  = debug_backtrace();
    $msg  = "{$call_id}:\n";

    foreach ($r as $row) {
      $msg  .= "{$row['file']}:{$row['line']}\n";
    }

    $this->log('debug_backtrace_log', $msg);
  }
}
