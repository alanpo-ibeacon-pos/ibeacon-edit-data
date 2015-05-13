<?php
function StringDateTime_Hongkong_UTC($from) {
    return DateTime::createFromFormat("Y-m-d H:i:s", $from, new DateTimeZone('Asia/Hong_Kong'))
        ->setTimeZone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s');
}

function StringDateTime_UTC_Hongkong($from) {
    return DateTime::createFromFormat("Y-m-d H:i:s", $from, new DateTimeZone('UTC'))
        ->setTimeZone(new DateTimeZone('Asia/Hong_Kong'))->format('Y-m-d H:i:s');
}