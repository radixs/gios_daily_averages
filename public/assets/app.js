$(function() {

    var apiPath      = 'api/getSensorData?code=';
    var sensorSelect = $("#sensorSelect");

    var sensorTable = $('#sensorTable').DataTable({
        ajax: {
            url: apiPath+sensorSelect.val(),
            dataSrc: ''
        },
        columns: [
            { data: "day", title: "Date Recorded" },
            { data: "averageValue", title: "Average Daily Value" }
        ],
        paging: false
    });

    sensorSelect.selectmenu({
        select: function() {
            sensorTable.ajax.url( apiPath+$(this).val() ).load();
        }
    });

});