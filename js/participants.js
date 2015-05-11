function fieldImageUpload() {
    var context = '<input id="imageUpload" type="file" name="files[]" data-url="proc/file-in/" multiple />';

    return $(context)
        .fileupload({
            dataType: 'json',
            add: function(e, data) {
                console.log('added');
                data.submit();
            },
            done: function(e, data) {
                $.each(data.result.files, function (index, file) {
                    $('.previewPane img.participantAvatar')
                        .attr('src', participantImgRoot + '/' + file.name)
                        .data('delUrl', file.deleteUrl)
                        .data('delType', file.deleteType);
                    console.log($('.editParticipantPhoto input[name=photo]'), file.name);
                    $('.editParticipantPhoto input[name=photo]').val(file.name);
                });
            },
            progressall: function(e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);

                console.log(progress);

                $('#progress .progress-bar').css(
                    'width',
                    progress + '%'
                );
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
        pageSize: 10,
        sorting: true,
        defaultSorting: 'participantId ASC',
        fields: {
            photo: {
                title: '',
                sorting: false,
                width: '1%',
                display: function(data) {
                    return fieldParticipantImage(data.record.photo);
                },
                input: function(data) {
                    var input = $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', 'photo');
                    data && data.record && data.record.photo && input.val(data.record.photo);

                    var previewPaneImg = null;
                    if (data && data.record && data.record.photo)
                        previewPaneImg = fieldParticipantImage(data.record.photo);
                    else
                        previewPaneImg = fieldParticipantImage();


                    var ret = $('<div>')
                        .toggleClass('editParticipantPhoto')
                        .append(input)
                        .append($('<div>')
                            .toggleClass('previewPane')
                            .append(previewPaneImg))
                        .append($('<div>')
                            .toggleClass('jtable-input-label')
                            .html('Upload new avatar'))
                        .append(fieldImageUpload())
                        .append($('<div>')
                            .attr('id','progress')
                            .toggleClass('progress')
                            .append($('<div>')
                                .toggleClass('progress-bar progress-bar-success')));

                    return ret;
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