# PHP MySQL Connection Helper

**Usage example:**
    
``` php
// CONSTRUCT
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
$result = $db->query("SELECT * FROM sample");
$fetchAll = $db->fetchAll($result);

// FETCH ASSOC
$result = $db->query("SELECT description, quantity FROM sample");
$fetchAssoc = "";
while($row = $db->fetchAssoc($result)) {
	$fetchAssoc .= $row["quantity"]."-".$row["description"]." / ";
}

$db->close();
```
