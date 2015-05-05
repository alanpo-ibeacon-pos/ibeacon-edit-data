<?php

require_once('../../cred.php');
$organizerId = Credentials::getOrganizerId();

//Open database connection
$db = new mysqli('localhost:3306', 'root', 'root', '2014fyp_ips');

//Get records from database
$stmt = $db->prepare("SELECT CONCAT(i.iBeaconId, ' (', HEX(uuid), ', ', major, ', ', minor, ')') AS DisplayText, i.iBeaconId AS Value FROM iBeacon i
                      INNER JOIN organizer_ibeacon io ON i.iBeaconId = io.iBeaconId
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