$(document).ready(function () {
    // datatable js start
    new DataTable('#customers-table', {
        layout: {},
        "ordering": false,
        oLanguage: {
            sLengthMenu: "_MENU_",
        }
    });
    // datatable js end
});
