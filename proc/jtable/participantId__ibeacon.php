<?php

require_once('../cred.php');
$organizerId = Credentials::getOrganizerId();

try
{
    if (empty($_POST["participantId"])) throw new Exception('No "participantId" POST param is given.');

    //Open database connection
    $db = new mysqli('moodle-db.cndunymmm6cz.ap-southeast-1.rds.amazonaws.com:3306', '2014fyp_ips', 'alanpo2593', '2014fyp_ips');

    //Getting records (listAction)
    if($_GET["action"] == "list")
    {

        //Get record count
        $stmt = $db->prepare("SELECT COUNT(1) AS RecordCount FROM participant_iBeacon pi
                              INNER JOIN organizer_iBeacon io ON pi.iBeaconId = io.iBeaconId
                              WHERE pi.participantId = ? AND io.organizerId = ?");

        $stmt->bind_param('ss', $_POST["participantId"], $organizerId);
        $stmt->execute();
        if ($stmt->error) throw new Exception('stmt err: ' . $stmt->error);
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $recordCount = $row['RecordCount'];

        //Get records from database
        $stmt = $db->prepare("SELECT i.iBeaconId, HEX(i.uuid) AS uuid, i.major, i.minor FROM iBeacon i
                              INNER JOIN participant_iBeacon pi ON i.iBeaconId = pi.iBeaconId
                              INNER JOIN organizer_iBeacon io ON pi.iBeaconId = io.iBeaconId
                              WHERE pi.participantId = ? AND io.organizerId = ?");
        $stmt->bind_param('ss', $_POST["participantId"], $organizerId);
        $stmt->execute();
        if ($stmt->error) throw new Exception('stmt err: ' . $stmt->error);
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
        $stmt->bind_param('ss', $_POST["participantId"], $_POST["c_iBeaconId"]);
        $stmt->execute();
        if ($stmt->error) throw new Exception($stmt->error);

        //Get last inserted record (to return to jTable)
        $stmt = $db->prepare("SELECT * FROM participant_iBeacon WHERE participantId = ? AND iBeaconId = ?");
        $stmt->bind_param('ss', $_POST["participantId"], $_POST["c_iBeaconId"]);
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
        if ($db->error) throw new Exception($db->error);

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