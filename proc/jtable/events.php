<?php

require_once('../include/dateconv.php');

try
{
	//Open database connection
	$db = $db = new mysqli('moodle-db.cndunymmm6cz.ap-southeast-1.rds.amazonaws.com:3306', '2014fyp_ips', 'alanpo2593', '2014fyp_ips');

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
            $row['startTime'] = StringDateTime_UTC_Hongkong($row['startTime']);
            $row['endTime'] = StringDateTime_UTC_Hongkong($row['endTime']);
            $row['lastUpdate'] = StringDateTime_UTC_Hongkong($row['lastUpdate']);
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
        $newIdx = $db->query('SELECT CONVERT(SUBSTR(MAX(eventId), 2), UNSIGNED) + 1 FROM event')->fetch_row()[0];
        $newID = 'E' . str_pad($newIdx, 7, '0', STR_PAD_LEFT);

		//Insert record into database
		$stmt = $db->prepare("INSERT INTO event(eventId, organizerId, locationId, name, description, startTime, endTime, inChargeName, inChargePhone)
                              VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('sssssssss',
            $newID, $_POST["organizerId"], $_POST["locationId"], $_POST["name"], $_POST["description"],
            StringDateTime_Hongkong_UTC($_POST["startTime"]),
            StringDateTime_Hongkong_UTC($_POST["endTime"]),
            $_POST["inChargeName"], $_POST["inChargePhone"]);
		if (!$stmt->execute()) throw new Exception($stmt->error);

		//Get last inserted record (to return to jTable)
		$result = $db->query("SELECT * FROM event WHERE eventId = LAST_INSERT_ID()");
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
		$stmt = $db->prepare("UPDATE event SET organizerId = ?, locationId = ?, name = ?, description = ?,
                              startTime = ?, endTime = ?, inChargeName = ?, inChargePhone = ? WHERE eventId = ?");
        $stmt->bind_param('sssssssss',
            $_POST["organizerId"], $_POST["locationId"], $_POST["name"], $_POST["description"],
            StringDateTime_Hongkong_UTC($_POST["startTime"]),
            StringDateTime_Hongkong_UTC($_POST["endTime"]),
            $_POST["inChargeName"], $_POST["inChargePhone"], $_POST['eventId']);
        if (!$stmt->execute()) throw new Exception($stmt->error);

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
		$stmt = $db->prepare("DELETE FROM event WHERE eventId = ?");
        $stmt->bind_param('s', $_POST['eventId']);
        if (!$stmt->execute()) throw new Exception($stmt->error);

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