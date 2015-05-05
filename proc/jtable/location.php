<?php

try
{
    //Open database connection
    $db = new mysqli('localhost:3306', 'root', 'root', '2014fyp_ips');

    //Getting records (listAction)
    if($_GET["action"] == "list")
    {
        //Get record count
        $result = $db->query("SELECT COUNT(1) AS RecordCount FROM location");
        $row = $result->fetch_assoc();
        $recordCount = $row['RecordCount'];

        //Get records from database
        $result = $db->query("SELECT * FROM location ORDER BY " . $_GET["jtSorting"] . " LIMIT " . $_GET["jtStartIndex"] . ", " . $_GET["jtPageSize"] . "");

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
        $newIdx = $db->query('SELECT CONVERT(SUBSTR(MAX(locationId), 4), UNSIGNED) + 1 FROM location')->fetch_row()[0];
        $newID = 'LOC' . str_pad($newIdx, 5, '0', STR_PAD_LEFT);

        //Insert record into database
        $stmt = $db->prepare("INSERT INTO location(locationId, name) VALUES (?, ?)");
        $stmt->bind_param('ss',$newID, $_POST["name"]);
        if (!$stmt->execute()) throw new Exception($stmt->error);

        //Get last inserted record (to return to jTable)
        $result = $db->query("SELECT * FROM location WHERE locationId = LAST_INSERT_ID()");
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
        $stmt = $db->prepare("UPDATE location SET name = ? WHERE locationId = ?");
        $stmt->bind_param('ss',
            $_POST["name"], $_POST["locationId"]);
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
        $stmt = $db->prepare("DELETE FROM location WHERE locationId = ?");
        $stmt->bind_param('s', $_POST["locationId"]);
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