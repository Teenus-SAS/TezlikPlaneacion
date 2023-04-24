$(document).ready(function() {
    $('input[type="file"]').on('change', function() {
        var ext = $(this).val().split('.').pop();
        if ($(this).val() != '') {
            if (ext != "xls" && ext != "xlsx") {
                $(this).val('');
                toastr.error(`La Extensión <b>${ext}</b> no es valida. Solo se aceptan archivos con extensión xls o xlsx`, "Intente Nuevamente...");
            }
        }
    });
});