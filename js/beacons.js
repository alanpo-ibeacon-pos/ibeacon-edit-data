function previewCsvBeacons() {
    setBeaconError(null);

    try {
        var inStr = $('#batch-input').val();

        if (inStr.length === 0) {
            throw 'Data input field has nothing.';
        }

        var csvLines = inStr.split(/\r\n|\r|\n/);
        // UUID, major, minor
        // no title row
        var beacons = parseCsvBeacons(csvLines);
    } catch (e) {
        setBeaconError(e);
        return;
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

        var uuid = cols[0].trim();
        var major = parseInt(cols[1].trim());
        var minor = parseInt(cols[2].trim());

        if (uuid.length === 0 || isNaN(major) || isNaN(minor)) throw 'Incorrect parsed CSV row format.';

        doms.push({"uuid": uuid, "major": major, "minor": minor});
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

function insertBeacon() {
    setBeaconError(null);

    var beacons = null;
    try {
        beacons = parseCsvBeacons([$('input#inUuid').val() + ',' +
                                      $('input#inMajor').val() + ',' +
                                      $('input#inMinor').val()]);
    } catch (e) {
        setBeaconError(e.message);
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

$.getScript("js/__beacon_tmpl.js", function() {
    insertBeaconContext('#tab-beacon');
    $("#tryParse").button().click(previewCsvBeacons);
    $("#import").button().click(importCsvBeacons);
    $("#insert").button().click(insertBeacon);
});