function fieldImageUpload() {
    var context = '<input id="imageUpload" type="file" name="files[]" data-url="server/php/" multiple>';

    return $(context)
        .fileupload({
            dataType: 'json',
            done: function (e, data) {
                $.each(data.result.files, function (index, file) {
                    $('<p/>').text(file.name).appendTo(document.body);
                });
            }
        });
}

$(function() {

    //Prepare jTable
    $('#ParticipantTableContainer').jtable({
        title: 'Participants List',
        actions: {
            listAction: 'proc/jtable/participant.php?action=list',
            createAction: 'proc/jtable/participant.php?action=create',
            updateAction: 'proc/jtable/participant.php?action=update',
            deleteAction: 'proc/jtable/participant.php?action=delete'
        },
        selecting: false,
        paging: true,
        pageSize: 2,
        sorting: true,
        defaultSorting: 'participantId ASC',
        fields: {
            photo: {
                title: '',
                sorting: false,
                width: '1%',
                display: function(data) {
                    return '<img class="participantAvatar" src="' + participantImgRoot + '/' + data.record.photo + '" />'
                },
                input: function(data) {
                    return fieldImageUpload() + '';
                }
            },
            participantId: {
                title: '#',
                key: true,
                create: false,
                edit: false
            },
            name: {
                title: 'Name'
            },
            phone: {
                title: 'Phone'
            },
            gender: {
                title: 'Gender',
                options: ['M', 'F', 'X']
            },
            address: {
                title: 'Address'
            },
            emergencyName: {
                title: 'Emg. Name'
            },
            emergencyPhone: {
                title: 'Emg. Phone'
            }
        }
    }).jtable('load');

});