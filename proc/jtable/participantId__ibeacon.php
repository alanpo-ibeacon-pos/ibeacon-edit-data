<?php
// SELECT * FROM participant p INNER JOIN participant_Event pe ON p.participantId = pe.participantId WHERE pe.eventId = "E0000001"
try
{
    if (empty($_POST["participantId"])) throw new Exception('No "participantId" POST param is given.');

    //Open database connection
    $db = new mysqli('localhost:3306', 'root', 'root', '2014fyp_ips');

    //Getting records (listAction)
    if($_GET["action"] == "list")
    {

        //Get record count
        $stmt = $db->prepare("SELECT COUNT(1) AS RecordCount FROM participant_iBeacon
                              WHERE participantId = ?");
        $stmt->bind_param('s', $_POST["participantId"]);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $recordCount = $row['RecordCount'];

        //Get records from database
        $stmt = $db->prepare("SELECT iBeacon.iBeaconId, HEX(iBeacon.uuid), iBeacon.major, iBeacon.minor FROM iBeacon
                              INNER JOIN participant_iBeacon ON iBeacon.iBeaconId = participant_iBeacon.iBeaconId
                              WHERE participant_iBeacon.participantId = ?");
        $stmt->bind_param('s', $_POST["participantId"]);
        $stmt->execute();
        if ($stmt->error) throw new Exception($stmt->error);
        $result = $stmt->get_result();

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
        $stmt = $db->prepare("INSERT INTO participant_iBeacon (participantId, iBeaconId) VALUES(?, ?)");
        $stmt->bind_param('ss', $_POST["participantId"], $_POST["iBeaconId"]);
        $stmt->execute();
        if ($stmt->error) throw new Exception($stmt->error);

        //Get last inserted record (to return to jTable)
        $stmt = $db->prepare("SELECT * FROM participant_iBeacon WHERE participantId = ? AND iBeaconId = ?");
        $stmt->bind_param('ss', $_POST["participantId"], $_POST["iBeaconId"]);
        $stmt->execute();
        if ($stmt->error) throw new Exception($stmt->error);
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        //Return result to jTable
        $jTableResult = array();
        $jTableResult['Result'] = "OK";
        $jTableResult['Record'] = $row;

        header('Content-Type: application/json');
        print json_encode($jTableResult);
    }
    //Deleting a record (deleteAction)
    else if($_GET["action"] == "delete")
    {
        //Delete from database
        $stmt = $db->prepare("DELETE FROM participant_iBeacon WHERE participantId = ? AND iBeaconId = ?");
        $stmt->bind_param('ss', $_POST["participantId"], $_POST["iBeaconId"]);
        $stmt->execute();
        if ($stmt->error) throw new Exception($stmt->error);

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