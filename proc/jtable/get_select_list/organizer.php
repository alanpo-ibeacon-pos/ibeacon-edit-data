<?php
//Open database connection
$db = $db = new mysqli('moodle-db.cndunymmm6cz.ap-southeast-1.rds.amazonaws.com:3306', '2014fyp_ips', 'alanpo2593', '2014fyp_ips');

//Get records from database
$result = $db->query("SELECT `organizerId` AS 'Value', `name` AS 'DisplayText' FROM organizer");

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