<?php
require_once "mysql.php";

// host user pass db
$db = new MySQL("localhost", "root", "password", "sample");

// SELECT
$result = $db->query("SELECT id, description, quantity FROM sample");

// NUM ROWS
$numRows = $db->numRows($result);

// Get next ID
$newId = $db->newId("sample", "id");

// UPDATE
$updateStatus = $db->updateRow("sample", array(
		"id" => 1
	), array(
		"quantity" => 1,
		"description" => "One"
	));

// INSERT
$insertStatus = $db->insertRow("sample", array(
		"description" => "teste",
		"quantity" => 6
	));

// DELETE
$deleteStatus = $db->deleteRow("sample", array(
		"quantity" => 6
	));

// FETCH ALL
$result = $db->query("SELECT id, description, quantity FROM sample");
$fetchAll = $db->fetchAll($result);

// FETCH ASSOC
$result = $db->query("SELECT description, quantity FROM sample");
$fetchAssoc = "";
while($row = $db->fetchAssoc($result)) {
	$fetchAssoc .= $row["quantity"]."-".$row["description"]." / ";
}

$db->close();

// RESULTS
echo
	"Total rows: $numRows <br />".
	"Next ID: $newId <br />".
	"Update query status: ".($updateStatus ? "ok" : "error")."<br />".
	"Insert query status: ".($insertStatus ? "ok" : "error")."<br />".
	"Delete query status: ".($deleteStatus ? "ok" : "error")."<br />".
	"Select result (fetchAssoc): ".$fetchAssoc."<br />".
	"Select result (fetchAll): <pre>";
var_dump($fetchAll);
echo "</pre>"
?>