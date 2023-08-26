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

        loadTblOffices(firtsDate, lastDate);
    });

    // $(document).on('change', '.dateOrders', async function (e) {
    //     e.preventDefault();

    //     let dateOrders = document.getElementsByClassName('dateOrders');
    //     let status = true;

    //     for (let i = 0; i < dateOrders.length; i++) {
    //         if (dateOrders[i].value == '') {
    //             status = false;
    //             break;
    //         }
    //     }

    //     if (status == false) {
    //         toastr.error('Ingrese Fecha Inicial y Fecha Final');
    //     } else {
    //         loadTblOffices(dateOrders[0].value, dateOrders[1].value);
    //     }
    // });

    /* Mensaje de exito */
    message = (data) => {
        if (data.success == true) {
            loadTblOffices(null, null);
            $('.cardAddDate').hide(800);
            $('#formAddDate').trigger('reset');
            toastr.success(data.message);
            return false;
        } else if (data.error == true) toastr.error(data.message);
        else if (data.info == true) toastr.info(data.message);
    };

    /* Actualizar tabla 
    function updateTable() {
        $('#tblOffices').DataTable().clear();
        $('#tblOffices').DataTable().ajax.reload();
    } */
});