$(function() {

    //Prepare jTable
    $('#LocationTableContainer').jtable({
        title: 'Locations List',
        actions: {
            listAction: 'proc/jtable/location.php?action=list',
            createAction: 'proc/jtable/location.php?action=create',
            updateAction: 'proc/jtable/location.php?action=update',
            deleteAction: 'proc/jtable/location.php?action=delete'
        },
        selecting: false,
        paging: true,
        pageSize: 10,
        sorting: true,
        defaultSorting: 'locationId ASC',
        fields: {
            locationId: {
                title: '#',
                key: true,
                create: false,
                edit: false
            },
            name: {
                title: 'Name'
            }
        }
    }).jtable('load');

});