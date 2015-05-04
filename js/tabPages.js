$("#tabs").tabs().on("tabsactivate", function( event, ui ) {
    window.location.href = ui.newTab.find('a.ui-tabs-anchor').attr('href');
});