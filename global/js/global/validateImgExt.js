$(document).ready(function() {
    //$('input[type="file"]').on('change', function() {
    $('#formFile').on('change', function () { 
        var ext = $(this).val().split('.').pop();
        if ($(this).val() != '') {
            if (ext != "png" && ext != "jpg" && ext != "jpge") {
                $(this).val('');
                toastr.error(`La Extensión <b>${ext}</b> no es valida. Solo se aceptan archivos con extensión png, jpg, jpge`, "Intente Nuevamente...");
            }
        }
    });
});