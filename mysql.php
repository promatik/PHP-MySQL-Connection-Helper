<?php	

class MySQL extends mysqli
{
	private $log = false;
	public $logs = array();
	public $connected = false;
	public $queryCount = 0;
	
	public function __construct($host, $user, $pass, $database, $log = false) {
		parent::__construct($host, $user, $pass, $database);
		$this->connected = true;
		$this->log = $log;
		
		if(mysqli_connect_errno()) {
			throw new Exception("Connection failed: ".mysqli_connect_error());
			$this->connected = false;
		}
	}
	
	public function __destruct() {
		$this->close();
	}
	
	public function close() {
		if($this->connected) {
			parent::close();
			$this->connected = false;
		}
	}
	
	private function _query($query, $escape_query = true) {
		if($escape_query)
			$query = preg_replace(array("/\\\\r|\\\\n/", "/\\t{1,}/", "/\\\'/", "/\\s{2,}/"), array("", " ", "'", " "), utf8_encode(parent::escape_string($query)));
		
		$this->queryCount++;
		if($this->log) {
			$logTime = microtime(true);
			$r = parent::query($query);
			
			array_push($this->logs, array(
				"query" => "'$query'",
				"rows" => $this->affected_rows,
				"info" => $this->info,
				"errors" => $this->errno,
				"time" => (microtime(true) - $logTime) * 1000
			));
			return $r;
		} else {
			return parent::query($query);
		}
	}
	
	public function query($query, $escape_query = true) {
		return $this->_query($query);
	}
	
	public function fetchQuery($query) {
		return $this->fetchAll($this->_query($query));
	}
	
	public function fetchAssocQuery($query) {
		return $this->fetchAssoc($this->_query($query));
	}
	
	public function numRows($result) {
		return $result->num_rows;
	}
	
	public function select($table, $fields = "*", $where = null, $order = null, $limit = null) {
		if($where) array_walk($where, array($this, 'mapKeyVal'));
		if($order) array_walk($order, array($this, 'mapOrder'));
		if(is_string($fields)) $fields = preg_split("/,\s|\s|,/", $fields);
		array_walk($fields, array($this, 'mapFields'));
		
		return $this->_query("SELECT ".(is_array($fields) ? join(", ", $fields) : $fields)." FROM `$table`".($where ? " WHERE ".join(" AND ", $where) : "").($order ? " ORDER BY ".join(", ", $order) : "").($limit ? " LIMIT $limit" : "") . ";");
	}
	
	public function updateRow($table, $index, $data) {
		array_walk($index, array($this, 'mapKeyVal'));
		array_walk($data, array($this, 'mapKeyVal'));
		
		return $this->_query("UPDATE `$table` SET ".join(", ", $data)." WHERE ".join(" AND ", $index).";");
	}
	
	public function insertRow($table, $data) {
		$cols = array_map(array($this, "accentString"), array_keys(($data)));
		$vals = array_map(array($this, "apostropheString"), $data);
		
		$res = $this->_query("INSERT INTO `$table` (" . join(", ", $cols) . ") VALUES (" . join(", ", $vals) . ");");
		return $res ? $this->insert_id : false;
	}
	
	public function deleteRow($table, $index) {
		array_walk($index, array($this, 'mapKeyVal'));
		
		return $this->_query("DELETE FROM `$table` WHERE ".join(" AND ", $index).";");
	}
	
	public function newId($table, $field) {
		$resource = $this->query("SELECT MAX($field) + 1 n FROM `$table`;");
		$result = $resource->fetch_assoc();
		return $result["n"];
	}
	
	public function fetchAssoc($result) {
		return $result->fetch_assoc();
	}
	
	public function fetchAll($result) {
		$res = array();
		while($r = $result->fetch_assoc())
			array_push($res, $r);
		return $res;
	}

	// Helper
	private function mapKeyVal(&$v, $k) {
		$k = explode(" ", $k);
		$v = $this->accentString(array_shift($k)) . " ". (sizeof($k) ? join(" ", $k) : " = ") . " " . (strpos($v, ' ') !== false ? $v : $this->apostropheString($v));
	}
	
	private function mapFields(&$v, &$k) {
		$v = $v == "*" ? $v : $this->accentString($v);
	}
	
	private function mapOrder(&$v, $k) {
		$v = (is_int($k) ? "" : $this->accentString($k)) . " ". (is_int($k) ? $this->accentString($v) : $v);
	}

	private function accentString($s) {
		return is_string($s) ? "`$s`" : $s;
	}

	private function apostropheString($s) {
		return is_string($s) ? "'$s'" : (is_null($s) ? "''" : $s);
	}
}

?>