<?php

require_once('../../cred.php');
$organizerId = Credentials::getOrganizerId();

//Open database connection
$db = $db = new mysqli('moodle-db.cndunymmm6cz.ap-southeast-1.rds.amazonaws.com:3306', '2014fyp_ips', 'alanpo2593', '2014fyp_ips');

//Get records from database
$stmt = $db->prepare("SELECT CONCAT(i.iBeaconId, ' (', HEX(uuid), ', ', major, ', ', minor, ')') AS DisplayText, i.iBeaconId AS Value FROM iBeacon i
                      INNER JOIN organizer_iBeacon io ON i.iBeaconId = io.iBeaconId
                      WHERE io.organizerId = ?");
$stmt->bind_param('s', $organizerId);
$stmt->execute();
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
$jTableResult['Options'] = $rows;

header('Content-Type: application/json');
print json_encode($jTableResult);

$db->close();