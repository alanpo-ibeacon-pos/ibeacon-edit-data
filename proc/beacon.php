<?php
/*
 * in:
 *  string of json of beacons -> { uuid, major, minor }
 * out:
 *  GOOD: http response 200
 *  BAD:  http response 500 == error + content for err msg
 */

require_once('cred.php');
$db = null;

try {
    if (empty($_POST['do']) || empty($_POST['data']) || $_POST['do'] != 'insert') throw new Exception('Incomplete POST arguments.');
    $json = $_POST['data'];

    $in = json_decode($json);

    //Open database connection
    $db = $db = new mysqli('moodle-db.cndunymmm6cz.ap-southeast-1.rds.amazonaws.com:3306', '2014fyp_ips', 'alanpo2593', '2014fyp_ips');

    $newIdx = $db->query('SELECT CONVERT(SUBSTR(MAX(iBeaconId), 2), UNSIGNED) + 1 FROM iBeacon')->fetch_row()[0];

    $db->autocommit(false);
    //Insert record into database
    $stmt = $db->prepare("INSERT INTO iBeacon (iBeaconId, uuid, major, minor) VALUES (?, ?, ?, ?)");
    if ($db->error) throw new Exception('stmt fuur iBeacon: ' . $db->error);
    $stmt->bind_param('ssii', $newID, $i_uuid, $i_major, $i_minor);

    $stmt2 = $db->prepare("INSERT INTO organizer_iBeacon (iBeaconId, organizerId) VALUES (?, ?)");
    if ($db->error) throw new Exception('stmt fuur iBeacon: ' . $db->error);
    $stmt2->bind_param('ss', $newID, $oid);


    foreach ($in as $bcn) {
        $newID = 'B' . str_pad($newIdx++, 7, '0', STR_PAD_LEFT);
        $oid = Credentials::getOrganizerId();
        $i_uuid = pack("H*", $bcn->uuid);
        $i_major = $bcn->major;
        $i_minor = $bcn->minor;
        $stmt->execute();
        $stmt2->execute();
        if ($stmt->error) throw new Exception($stmt->error);
    }
    $db->commit();
    if ($db->error) throw new Exception($db->error);
    $db->close();

    http_response_code(200);
} catch(Exception $ex) {
    http_response_code(500);
    header('Content-Type: text/plain');
    print $ex->getMessage();

    if (!is_null($db)) {
        $db->rollback();
        $db->close();
    }
}