<?php

/*
	[Discuz!] (C)2001-2006 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	Rewritten for PHP 7.4: mysql_* → mysqli_*
	Updated for PHP 8.4: disable mysqli exception mode
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class dbstuff {
	public $querynum = 0;
	public $conn = null;

	function connect($dbhost, $dbuser, $dbpw, $dbname = '', $pconnect = 0) {
		if($pconnect) {
			$this->conn = @new mysqli('p:' . $dbhost, $dbuser, $dbpw);
		} else {
			$this->conn = @new mysqli($dbhost, $dbuser, $dbpw);
		}

		if($this->conn->connect_error) {
			$this->halt('Can not connect to MySQL server');
		}

		if($this->version() > '4.1') {
			global $charset, $dbcharset;
			if(!$dbcharset && in_array(strtolower($charset), array('gbk', 'big5', 'utf-8'))) {
				$dbcharset = str_replace('-', '', $charset);
			}

			if($dbcharset) {
				$this->conn->query("SET character_set_connection=$dbcharset, character_set_results=$dbcharset, character_set_client=binary");
			}

			if($this->version() > '5.0.1') {
				$this->conn->query("SET sql_mode=''");
			}
		}

		if($dbname) {
			$this->conn->select_db($dbname);
		}

		// PHP 8.1+ defaults to MYSQLI_REPORT_ERROR|MYSQLI_REPORT_STRICT,
		// which throws exceptions instead of returning false. Disable to
		// preserve legacy error-handling behavior in query().
		$this->conn->report_mode = MYSQLI_REPORT_OFF;

	}

	function select_db($dbname) {
		return $this->conn->select_db($dbname);
	}

	function fetch_array($query, $result_type = MYSQLI_ASSOC) {
		return mysqli_fetch_array($query, $result_type);
	}

	function query($sql, $type = '') {
		if($type == 'UNBUFFERED') {
			$query = @$this->conn->query($sql, MYSQLI_USE_RESULT);
		} else {
			$query = @$this->conn->query($sql);
		}
		if(!$query && $type != 'SILENT') {
			$this->halt('MySQL Query Error', $sql);
		}
		$this->querynum++;
		return $query;
	}

	function affected_rows() {
		return $this->conn->affected_rows;
	}

	function error() {
		return $this->conn->error;
	}

	function errno() {
		return intval($this->conn->errno);
	}

	function result($query, $row) {
		if($query && mysqli_data_seek($query, $row)) {
			$result = mysqli_fetch_row($query);
			return $result[0];
		}
		return false;
	}

	function num_rows($query) {
		return mysqli_num_rows($query);
	}

	function num_fields($query) {
		return mysqli_num_fields($query);
	}

	function free_result($query) {
		return mysqli_free_result($query);
	}

	function insert_id() {
		return $this->conn->insert_id;
	}

	function fetch_row($query) {
		return mysqli_fetch_row($query);
	}

	function fetch_fields($query) {
		return mysqli_fetch_field($query);
	}

	function version() {
		return $this->conn->server_info;
	}

	function close() {
		return $this->conn->close();
	}

	function halt($message = '', $sql = '') {
		require_once DISCUZ_ROOT.'./include/db_mysql_error.inc.php';
	}
}

?>
