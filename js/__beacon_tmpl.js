function insertBeaconContext(jqSelector) {
    $('<div id="beaconErrorArea" class="ui-widget">\
    <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">\
        <p>\
            <span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>\
            <strong>Error: </strong><span class="msg"></span>\
        </p>\
    </div>\
</div>\
<div id="beaconAlertArea" class="ui-widget">\
    <div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;">\
        <p>\
            <span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>\
            <span class="msg"></span>\
        </p>\
    </div>\
</div>\
<div id="form-batch-ibeacon" class="formSection">\
    <div class="formTitle" style="width: 6em;">Batch Import</div>\
    <button id="import">Import</button>\
    <p>Paste the CSV data in the text area on the left, and preview them on the right. After that, press "Import" button on the right.</p>\
    <div class="floatRight w50">\
        <button id="tryParse" class="floatRight">Try Parse</button>\
        <h2>Preview: </h2>\
        <div class="h300px" id="batch-preview">\
        </div>\
    </div>\
    <div class="w50">\
        <h2>Data: </h2>\
        <textarea class="h300px" value="" id="batch-input" onchange=""></textarea>\
    </div>\
    <div class="clear"></div>\
</div>\
<div id="form-single-ibeacon" class="formSection">\
    <div class="formTitle" style="width: 7em;">Create iBeacon</div>\
    <button id="insert">Insert</button>\
    <p>Create single iBeacon into database.</p>\
    <div class="f1em5">\
        <label for="inUuid">UUID:</label>\
        <input type="text" id="inUuid" />\
        <br />\
        <label for="inMajor">Major:</label>\
        <input type="text" id="inMajor" />\
        <br />\
        <label for="inMinor">Minor:</label>\
        <input type="text" id="inMinor" />\
    </div>\
</div>').appendTo(jqSelector);
}