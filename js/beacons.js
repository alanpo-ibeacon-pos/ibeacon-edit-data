$("#tryParse").button().click(previewCsvBeacons);
$("#import").button().click(importCsvBeacons);
$("#insert").button();

function previewCsvBeacons() {
    setBeaconError(null);

    try {
        var csvLines = $('#batch-input').val().split(/\r\n|\r|\n/);
        // UUID, major, minor
        // no title row
        var beacons = parseCsvBeacons(csvLines);
    } catch (e) {
        setBeaconError(e.message);
    }

    $('#form-batch-ibeacon').data('beacons', beacons);
    $('#batch-preview').empty();
    for (var i in beacons) {
        createDomPreviewBeacon(beacons[i]).appendTo('#batch-preview');
    }
}

function createDomPreviewBeacon(beacon) {
    return $("<div>", {
        "class": "previewBeaconItem w100",
        "html": '<span class="uuid">' + beacon.uuid + '</span> <span class="major">' + beacon.major + '</span>, <span class="minor">' + beacon.minor + '</span>'
    })
        .data("uuid", beacon.uuid)
        .data("major", beacon.major)
        .data("minor", beacon.minor);
}

function parseCsvBeacons(lines) {
    // UUID, major, minor
    // no title row
    var doms = new Array();
    for (var i in lines) {
        var ln = lines[i];
        var cols = ln.split(',');
        doms.push({"uuid": cols[0].trim(), "major": parseInt(cols[1].trim()), "minor": parseInt(cols[2].trim())});
    }
    return doms;
}

function importCsvBeacons() {
    setBeaconError(null);

    var beacons = $('#form-batch-ibeacon').data('beacons');
    if (!beacons) {
        setBeaconError("No try-parsed beacons. Please press \"Parse\" first.");
        return;
    }

    var postParam = $.param({
        do: 'insert',
        data: JSON.stringify(beacons)
    });
    $.ajax('proc/beacon.php', {type: 'POST', data: postParam})
        .done(function(data, textStatus, jqXHR) {
            setBeaconAlert("Successfully added " + beacons.length + " new iBeacons into database.")
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR, textStatus, errorThrown);
            setBeaconError(jqXHR.responseText);
        });
}

function setBeaconError(msg) {
    var ctrln = $('#beaconErrorArea');
    if (msg) {
        ctrln.show();
        ctrln.find('.msg').text(msg);

        //setTimeout(setBeaconError, 5000);
    }
    else {
        ctrln.hide();
    }
}

function setBeaconAlert(msg) {
    var ctrln = $('#beaconAlertArea');

    if (msg) {
        ctrln.show();
        ctrln.find('.msg').text(msg);

        setTimeout(setBeaconAlert, 5000);
    }
    else {
        ctrln.hide();
    }
}