<?php
require_once "mysql.php";

// host user pass db
$db = new MySQL("localhost", "root", "password", "sample", true);

// QUERY
$result = $db->query("SELECT id, description, quantity FROM sample");

// NUM ROWS
$numRows = $db->numRows($result);

// Get next ID
$newId = $db->newId("sample", "id");

// SELECT
$selectSample = $db->select("sample",
	array("id", "description"), // fields
	array("quantity >" => 1), // where
	array("quantity" => "desc"), // order
	2); // limit

$fetchAllSelect = $db->fetchAll($selectSample);
	
// UPDATE
$updateStatus = $db->updateRow("sample", array(
		"id" => 1
	), array(
		"quantity" => 1,
		"description" => "One"
	));

// INSERT
$insertID = $db->insertRow("sample", array(
		"description" => "teste",
		"quantity" => 6
	));

// DELETE
$deleteStatus = $db->deleteRow("sample", array(
		"quantity" => 6
	));

// FETCH ALL
$result = $db->select("sample", array("id", "description"));
$fetchAll = $db->fetchAll($result);

// FETCH ASSOC
$result = $db->select("sample");
$fetchAssoc = "";
while($row = $db->fetchAssoc($result)) {
	$fetchAssoc .= $row["quantity"]."-".$row["description"]." / ";
}

$db->close();

// RESULTS
echo
	"Total rows: $numRows <br />".
	"Next ID: $newId <br />".
	"Update query status: ".($selectSample ? "ok" : "error")."<br />".
	"Update query status: ".($updateStatus ? "ok" : "error")."<br />".
	"Insert query id: $insertID<br />".
	"Delete query status: ".($deleteStatus ? "ok" : "error")."<br />".
	"Select result (fetchAll): <pre>";
var_dump($fetchAllSelect);
echo "</pre>";

echo
	"Query result (fetchAssoc): ".$fetchAssoc."<br />".
	"Query result (fetchAll): <pre>";
var_dump($fetchAll);
echo "</pre>";

echo "SQL Logs: <pre>";
var_dump($db->logs);
echo "</pre>";

?>