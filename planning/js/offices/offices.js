$(document).ready(function () {
    $(document).on('click', '.changeDate', function (e) {
        e.preventDefault();

        let date = new Date().toISOString().split('T')[0];
        let row = $(this).parent().parent()[0];
        let data = tblOffices.fnGetData(row);

        bootbox.confirm({
            title: 'Ingrese Fecha De Entrega!',
            message: `<div class="col-sm-12 floating-label enable-floating-label">
                        <input class="form-control" type="date" name="date" id="date" max="${date}"></input>
                        <label for="date">Fecha</span></label>
                      </div>`,
            buttons: {
                confirm: {
                    label: 'Agregar',
                    className: 'btn-success',
                },
                cancel: {
                    label: 'Cancelar',
                    className: 'btn-danger',
                },
            },
            callback: function (result) {
                if (result == true) {
                    let date = $('#date').val();

                    if (!date) {
                        toastr.error('Ingrese los campos');
                        return false;
                    }

                    let form = new FormData();
                    form.append('idOrder', data.id_order);
                    form.append('idProduct', data.id_product);
                    form.append('originalQuantity', data.original_quantity);
                    form.append('quantity', data.quantity);
                    form.append('stock', data.minimum_stock);
                    form.append('date', date);

                    $.ajax({
                        type: "POST",
                        url: '/api/changeOffices',
                        data: form,
                        contentType: false,
                        cache: false,
                        processData: false,
                        success: function (resp) {
                            message(resp);
                        }
                    });
                }
            },
        });
    });

    $('.cardSearchDate').hide();

    $('#btnOpenSearchDate').click(function (e) {
        e.preventDefault();

        $('.cardSearchDate').toggle(800);
        $('#formSearchDate').trigger('reset');
        let date = new Date().toISOString().split('T')[0];

        $('#lastDate').val(date);

        let maxDate = document.getElementById('lastDate');
        let minDate = document.getElementById('firtsDate');

        maxDate.setAttribute("max", date);
        minDate.setAttribute("max", date);
    });

    $('#btnSearchDate').click(async function (e) {
        e.preventDefault();
        
        let firtsDate = $('#firtsDate').val();
        let lastDate = $('#lastDate').val();
                
        if (!firtsDate || firtsDate == '' || !lastDate || lastDate == '') {
            toastr.error('Ingrese los campos');
            return false;
        }

        loadAllData(3, firtsDate, lastDate);
    });

    $(document).on('click', '.cancelOrder', function (e) {
        e.preventDefault();

        let row = $(this).parent().parent()[0];
        let data = tblOffices.fnGetData(row);

        bootbox.confirm({
            title: 'Cancelar Despacho',
            message: `Está seguro de cancelar este despacho? Esta acción no se puede reversar.`,
            buttons: {
                confirm: {
                    label: 'Si',
                    className: 'btn-success',
                },
                cancel: {
                    label: 'No',
                    className: 'btn-danger',
                },
            },
            callback: function (result) {
                if (result == true) {
                    let form = new FormData();
                    form.append('idOrder', data.id_order);
                    form.append('idProduct', data.id_product);
                    form.append('originalQuantity', data.original_quantity);
                    form.append('quantity', data.accumulated_quantity);

                    $.ajax({
                        type: "POST",
                        url: '/api/cancelOffice',
                        data: form,
                        contentType: false,
                        cache: false,
                        processData: false,
                        success: function (resp) {
                            message(resp);
                        }
                    });
                }
            },
        });
    });

    /* Mensaje de exito */
    message = (data) => {
        if (data.success == true) {
            // loadTblOffices(null, null);
            loadAllData(2, null, null);
            $('.cardAddDate').hide(800);
            $('#formAddDate').trigger('reset');
            toastr.success(data.message);
            return false;
        } else if (data.error == true) toastr.error(data.message);
        else if (data.info == true) toastr.info(data.message);
    };
 
});