function fieldDateTimePicker(name, data, sec) {
    sec = typeof sec === 'number' && sec > -1 && sec < 60 ? sec : 0;

    var val = '';
    try { val = data.record[name]; } catch (e) {}
    return $('<input type="text"/>')
        .attr('id', 'Edit-' + name)
        .attr('name', name)
        .val(val)
        .datetimepicker({
            format: 'Y-m-d H:i:' + (sec < 10 ? '0' + sec : sec),
            lang: 'en',
            mask: true
    });
}

$(function() {

    //Prepare jTable
    $('#EventTableContainer').jtable({
        title: 'Event List',
        actions: {
            listAction: 'proc/jtable/events.php?action=list',
            createAction: 'proc/jtable/events.php?action=create',
            updateAction: 'proc/jtable/events.php?action=update',
            deleteAction: 'proc/jtable/events.php?action=delete'
        },
        selecting: true,
        paging: true,
        pageSize: 10,
        sorting: true,
        defaultSorting: 'eventId ASC',
        selectionChanged: function (event, data) {
            var selectedRows = $('#EventTableContainer').jtable('selectedRows');
            if (selectedRows.length > 0) {
                var firstRowEventId = $(selectedRows[0]).data('record-key');
                $('#EventParticipantTableContainer')
                    .data('eventid', firstRowEventId)
                    .jtable('load', {eventid: firstRowEventId})
                    .find('.jtable-title-text').text('Participants for Event #' + firstRowEventId);
            }
        },
        fields: {
            eventId: {
                title: '#',
                key: true,
                create: false,
                edit: false
            },
            organizerId: {
                title: 'Organizer',
                options: 'proc/jtable/get_select_list/organizer.php'
            },
            locationId: {
                title: 'Location',
                options: 'proc/jtable/get_select_list/location.php'
            },
            name: {
                title: 'Name'
            },
            description: {
                title: 'Description'
            },
            startTime: {
                title: 'Start Time',
                display: function (data) {
                    return moment(data.record.startTime).format('YYYY-MM-DD HH:mm:ss');
                },
                input: function (data) {
                    return fieldDateTimePicker('startTime', data);
                }
            },
            endTime: {
                title: 'End Time',
                display: function (data) {
                    return moment(data.record.endTime).format('YYYY-MM-DD HH:mm:ss');
                },
                input: function (data) {
                    return fieldDateTimePicker('endTime', data, 59);
                }
            },
            lastUpdate: {
                title: 'Last Update',
                create: false,
                edit: false,
                display: function (data) {
                    return moment(data.record.lastUpdate).format('YYYY-MM-DD HH:mm:ss');
                }
            },
            inChargeName: {
                title: 'I/C Name'
            },
            inChargePhone: {
                title: 'I/C Phone'
            }
        }
    }).jtable('load');

    //Prepare jTable
    $('#EventParticipantTableContainer').jtable({
        title: 'Participants for Event (not selected)',
        actions: {
            listAction: function (postData, jtParams) {
                var eventId = $('#EventParticipantTableContainer').data('eventid');
                if (eventId === undefined || eventId === null || eventId.length == 0) return;
                postData['eventid'] = eventId;
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url: 'proc/jtable/eventId__participant.php?action=list&jtStartIndex=' + jtParams.jtStartIndex + '&jtPageSize=' + jtParams.jtPageSize + '&jtSorting=' + jtParams.jtSorting + '',
                        type: 'POST',
                        dataType: 'json',
                        data: postData,
                        success: function (data) {
                            $dfd.resolve(data);
                        },
                        error: function () {
                            $dfd.reject();
                        }
                    });
                });
            },
            createAction: function (postData) {
                var eventId = $('#EventParticipantTableContainer').data('eventid');
                console.log(eventId);
                if (eventId === undefined || eventId === null || eventId.length == 0) return;
                postData += '&eventid=' + eventId;
                console.log(typeof postData);
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url: 'proc/jtable/eventId__participant.php?action=create',
                        type: 'POST',
                        dataType: 'json',
                        data: postData,
                        success: function (data) {
                            $dfd.resolve(data);
                        },
                        error: function () {
                            $dfd.reject();
                        }
                    });
                });
            },
            deleteAction: function (postData) {
                console.log(postData);
                var eventId = $('#EventParticipantTableContainer').data('eventid');
                if (eventId === undefined || eventId === null || eventId.length == 0) return;
                postData['eventid'] = eventId;
                return $.Deferred(function ($dfd) {
                    $.ajax({
                        url: 'proc/jtable/eventId__participant.php?action=delete',
                        type: 'POST',
                        dataType: 'json',
                        data: postData,
                        success: function (data) {
                            $dfd.resolve(data);
                        },
                        error: function () {
                            $dfd.reject();
                        }
                    });
                });
            }
        },
        selecting: false,
        paging: true,
        pageSize: 10,
        sorting: true,
        defaultSorting: 'participantId ASC',
        fields: {
            ibeacon: {
                title: '',
                create: false,
                edit: false,
                sorting: false,
                width: '1%',
                display: function (item) {
                    //Create an image that will be used to open child table
                    var $img = $('<img class="participant-beacon-opener" src="img/ui/bluetooth.gif" title="Check iBeacons bound to this participant" />');
                    //Open child table when user clicks the image
                    $img.click(function () {
                        var openedFromRow = $('#EventParticipantTableContainer').data('openedChildTableFromRow');
                        if (openedFromRow != null) {
                            $('#EventParticipantTableContainer').jtable('closeChildTable', openedFromRow,
                                function(event, data) { $('#EventParticipantTableContainer').data('openedChildTableFromRow', null); });
                            if (openedFromRow[0] == $img.closest('tr')[0]) return;
                        }

                        $('#EventParticipantTableContainer').jtable('openChildTable',
                            $img.closest('tr'),
                            {
                                title: item.record.name + ' - bound iBeacons',
                                actions: {
                                    listAction: function (postData, jtParams) {
                                        postData = 'participantId=' + item.record.participantId;
                                        return $.Deferred(function ($dfd) {
                                            $.ajax({
                                                url: 'proc/jtable/participantId__ibeacon.php?action=list',
                                                type: 'POST',
                                                dataType: 'json',
                                                data: postData,
                                                success: function (data) {
                                                    $dfd.resolve(data);
                                                },
                                                error: function () {
                                                    $dfd.reject();
                                                }
                                            });
                                        });
                                    },
                                    createAction: function (postData) {
                                        // already has iBeaconId since it's key
                                        postData += '&participantId=' + item.record.participantId;
                                        return $.Deferred(function ($dfd) {
                                            $.ajax({
                                                url: 'proc/jtable/participantId__ibeacon.php?action=create',
                                                type: 'POST',
                                                dataType: 'json',
                                                data: postData,
                                                success: function (data) {
                                                    $dfd.resolve(data);
                                                },
                                                error: function () {
                                                    $dfd.reject();
                                                }
                                            });
                                        });
                                    },
                                    deleteAction: function (postData) {
                                        // already has iBeaconId since it's key
                                        postData += '&participantId=' + item.record.participantId;
                                        return $.Deferred(function ($dfd) {
                                            $.ajax({
                                                url: 'proc/jtable/participantId__ibeacon.php?action=delete',
                                                type: 'POST',
                                                dataType: 'json',
                                                data: postData,
                                                success: function (data) {
                                                    $dfd.resolve(data);
                                                },
                                                error: function () {
                                                    $dfd.reject();
                                                }
                                            });
                                        });
                                    }
                                },
                                fields: {
                                    c_iBeaconId: {
                                        list: false,
                                        create: true,
                                        edit: false,
                                        options: 'proc/jtable/get_select_list/ibeacon.php'
                                    },
                                    iBeaconId: {
                                        key: true,
                                        create: false,
                                        edit: false,
                                        title: 'iBeacon #'
                                    },
                                    uuid: {
                                        create: false,
                                        edit: false,
                                        title: 'UUID'
                                    },
                                    major: {
                                        create: false,
                                        edit: false,
                                        title: 'Major Value'
                                    },
                                    minor: {
                                        create: false,
                                        edit: false,
                                        title: 'Minor Value'
                                    }
                                }
                            }, function (data) { //opened handler
                                data.childTable.jtable('load');
                                $('#EventParticipantTableContainer').data('openedChildTableFromRow', $img.closest('tr'));
                            });
                    });
                    //Return image to show on the person row
                    return $img;
                }
            },
            pid: {
                list: false,
                create: true,
                options: 'proc/jtable/get_select_list/participant.php'
            },
            photo: {
                title: '',
                create: false,
                edit: false,
                sorting: false,
                width: '1%',
                display: function(data) {
                    return fieldParticipantImage(data.record.photo);
                }
            },
            participantId: {
                title: '#',
                key: true,
                create: false,
                edit: false
            },
            name: {
                title: 'Name',
                create: false,
                edit: false
            },
            phone: {
                title: 'Phone',
                create: false,
                edit: false
            },
            gender: {
                title: 'Gender',
                create: false,
                edit: false
            },
            address: {
                title: 'Address',
                create: false,
                edit: false
            },
            emergencyName: {
                title: 'Emg. Name',
                create: false,
                edit: false
            },
            emergencyPhone: {
                title: 'Emg. Phone',
                create: false,
                edit: false
            }
        }
    });

    //Load person list from server
    //$('#EventParticipantTableContainer').jtable('load');

});