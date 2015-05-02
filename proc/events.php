<?php

try
{
	//Open database connection
	$db = new mysqli('localhost:3306', 'root', 'root', '2014fyp_ips');

	//Getting records (listAction)
	if($_GET["action"] == "list")
	{
		//Get record count
		$result = $db->query("SELECT COUNT(1) AS RecordCount FROM event");
		$row = $result->fetch_assoc();
		$recordCount = $row['RecordCount'];

		//Get records from database
		$result = $db->query("SELECT * FROM event ORDER BY " . $_GET["jtSorting"] . " LIMIT " . $_GET["jtStartIndex"] . ", " . $_GET["jtPageSize"] . "");

		//Add all records to an array
		$rows = array();
		while($row = $result->fetch_assoc())
		{
		    $rows[] = $row;
		}

		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		$jTableResult['TotalRecordCount'] = $recordCount;
		$jTableResult['Records'] = $rows;

		header('Content-Type: application/json');
		print json_encode($jTableResult);
	}
	//Creating a new record (createAction)
	else if($_GET["action"] == "create")
	{
		//Insert record into database
		$stmt = $db->prepare("INSERT INTO traces(eventId, organizerId, locationId, name, description, startTime, endTime, lastUpdate, inChargeName, inChargePhone)
                              VALUES(:eventId, :organizerId, :locationId, :name, :description, :startTime, :endTime, now(), :inChargeName, :inChargePhone)");
		$stmt->execute(array(
			':eventId' => $_POST["eventId"],
			':organizerId' => $_POST["organizerId"],
			':locationId' => $_POST["locationId"],
			':name' => $_POST["name"],
			':description' => $_POST["description"],
			':startTime' => $_POST["startTime"],
			':endTime' => $_POST["endTime"],
			':inChargeName' => $_POST["inChargeName"],
			':inChargePhone' => $_POST["inChargePhone"]));

		//Get last inserted record (to return to jTable)
		$result = $db->query("SELECT * FROM event WHERE PersonId = LAST_INSERT_ID();");
		$row = $result->fetch_assoc();

		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		$jTableResult['Record'] = $row;

		header('Content-Type: application/json');
		print json_encode($jTableResult);
	}
	//Updating a record (updateAction)
	else if($_GET["action"] == "update")
	{
		//Update record in database
		$stmt = $db->prepare("UPDATE event SET organizerId = :organzerId, locationId = :locationId, name = :name, description = :description,
                              startTime = :startTime, endTime = :endTime, lastUpdate = now(), inChargeName = :inChargeName, inChargePhone = :inChargePhone WHERE eventId = :eventId");
		$stmt->execute(array(
			':organizerId' => $_POST["organizerId"],
			':locationId' => $_POST["locationId"],
			':name' => $_POST["name"],
			':description' => $_POST["description"],
			':startTime' => $_POST["startTime"],
			':endTime' => $_POST["endTime"],
			':lastUpdate' => $_POST["lastUpdate"],
			':inChargeName' => $_POST["inChargeName"],
			':inChargePhone' => $_POST["inChargePhone"],
			':eventId' => $_POST["eventId"]));

		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";

		header('Content-Type: application/json');
		print json_encode($jTableResult);
	}
	//Deleting a record (deleteAction)
	else if($_GET["action"] == "delete")
	{
		//Delete from database
		$stmt = $db->prepare("DELETE FROM event WHERE eventId = :eventId");
		$stmt->execute(array(':eventId' => $_POST["eventId"]));

		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";

		header('Content-Type: application/json');
		print json_encode($jTableResult);
	}

	//Close database connection
	$db->close();
}
catch(Exception $ex)
{
    //Return error message
	$jTableResult = array();
	$jTableResult['Result'] = "ERROR";
	$jTableResult['Message'] = $ex->getMessage();

	header('Content-Type: application/json');
	print json_encode($jTableResult);
}

?>