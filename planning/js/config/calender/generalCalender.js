$(document).ready(function () {
    $.ajax({ 
        url: '/api/saveCalenderAuto', 
        success: function (resp) {
            console.log(resp);
        }
    });
});