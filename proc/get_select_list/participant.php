<?php
//Open database connection
$db = new mysqli('localhost:3306', 'root', 'root', '2014fyp_ips');

//Get records from database
$result = $db->query("SELECT `participantId` AS 'Value', CONCAT(`participantId`,' - ',`name`) AS 'DisplayText' FROM participant");

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