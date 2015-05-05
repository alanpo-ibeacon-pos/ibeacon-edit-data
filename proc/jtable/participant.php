<?php

try
{
    //Open database connection
    $db = new mysqli('localhost:3306', 'root', 'root', '2014fyp_ips');

    //Getting records (listAction)
    if($_GET["action"] == "list")
    {
        //Get record count
        $result = $db->query("SELECT COUNT(1) AS RecordCount FROM participant");
        $row = $result->fetch_assoc();
        $recordCount = $row['RecordCount'];

        //Get records from database
        $result = $db->query("SELECT * FROM participant ORDER BY " . $_GET["jtSorting"] . " LIMIT " . $_GET["jtStartIndex"] . ", " . $_GET["jtPageSize"] . "");

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
        $newIdx = $db->query('SELECT CONVERT(SUBSTR(MAX(participantId), 2), UNSIGNED) + 1 FROM participant')->fetch_row()[0];
        $newID = 'P' . str_pad($newIdx, 7, '0', STR_PAD_LEFT);

        //Insert record into database
        $stmt = $db->prepare("INSERT INTO participant(participantId, name, phone, gender, address, photo, emergenceyName, emergencyPhone)
                              VALUES(?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssssss',
            $newID, $_POST["name"], $_POST["phone"], $_POST["gender"], $_POST["address"],
            $_POST["photo"], $_POST["emergencyName"], $_POST["emergencyPhone"]);
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
        $stmt = $db->prepare("UPDATE participant SET name = ?, phone = ?, gender = ?, address = ?, photo = ?, emergenceyName = ?, emergencyPhone = ? WHERE participantId = ?");
        $stmt->bind_param('ssssssss',
            $_POST["name"], $_POST["phone"], $_POST["gender"], $_POST["address"],
            $_POST["photo"], $_POST["emergencyName"], $_POST["emergencyPhone"], $_POST["participantId"]);
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
        $stmt = $db->prepare("DELETE FROM participant WHERE participantId = ?");
        $stmt->bind_param('s', $_POST["participantId"]);
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