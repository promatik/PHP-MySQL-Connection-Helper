<?php	

class MySQL extends mysqli
{
	private $connected = false;
	
	public function __construct($host, $user, $pass, $database) {
		parent::__construct($host, $user, $pass, $database);
		$this->connected = true;
		
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
	
	public function query($query, $escape_query = true) {
		if($escape_query)
			$query = preg_replace(array("/\\\\r|\\\\n/", "/\\t{1,}/", "/\\\'/"), array("", " ", "'"), utf8_encode(parent::escape_string($query)));
		return parent::query($query);
	}
	
	public function fetchQuery($query) {
		return self::fetchAll(parent::query($query));
	}
	
	public function numRows($result) {
		return $result->num_rows;
	}
	
	public function updateRow($table, $index, $data) {
		array_walk($index, array($this, 'mapKyeVal'));
		array_walk($data, array($this, 'mapKyeVal'));
		
		return parent::query("UPDATE $table SET ".join(", ", $data)." WHERE ".join(" AND ", $index).";");
	}
	
	public function insertRow($table, $data) {
		return parent::query("INSERT INTO `$table` (" . join(", ", array_map(array($this, "accentString"), array_keys(($data)))) . ") VALUES (" . join(", ", array_map(array($this, "apostropheString"), $data)) . ");");
	}
	
	public function deleteRow($table, $index) {
		array_walk($index, array($this, 'mapKyeVal'));
		
		return parent::query("DELETE FROM `$table` WHERE ".join(" AND ", $index).";");
	}
	
	public function newId($table, $field) {
		$resource = parent::query("SELECT MAX($field) + 1 n FROM `$table`;");
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
	private function mapKyeVal(&$v, $k) {
		$v = "`$k` = " . self::apostropheString($v);
	}

	private function accentString($s) {
		return is_string($s) ? "`$s`" : $s;
	}

	private function apostropheString($s) {
		return is_string($s) ? "'$s'" : $s;
	}
}

?>