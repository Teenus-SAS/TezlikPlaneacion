$(document).ready(function () {
    /* Ocultar panel crear stock */

    $('.cardCreateStock').hide();

    /* Abrir panel crear stock */

    $('#btnNewStock').click(function (e) {
        e.preventDefault();

        $('.cardImportStock').hide(800);
        $('.cardCreateStock').toggle(800);
        $('#formCreateStock').trigger('reset');
        $('#btnCreateStock').html('Crear');

        sessionStorage.removeItem('idStock');
    });

    /* Crear nuevo proceso */

    $('#btnCreateStock').click(function (e) {
        e.preventDefault();

        let idStock = sessionStorage.getItem('idStock');
        if (!idStock)
            checkDataStock('/api/addStock', idStock);
        else
            checkDataStock('/api/updateStock', idStock);
    });

    /* Actualizar procesos */

    $(document).on('click', '.updateStock', function (e) {
        $('.cardImportStock').hide(800);
        $('.cardCreateStock').show(800);
        $('#btnCreateStock').html('Actualizar');

        let row = $(this).parent().parent()[0];
        let data = tblStock.fnGetData(row);

        sessionStorage.setItem('idStock', data.id_stock);
        $(`#material option[value=${data.id_material}]`).prop('selected', true);
        $('#max').val(data.max_term);
        $('#usual').val(data.usual_term);

        $('html, body').animate(
            {
                scrollTop: 0,
            },
            1000
        );
    });

    checkDataStock = async (url, idStock) => {
        let material = parseFloat($('#material').val());
        let max = parseFloat($('#max').val());
        let usual = parseFloat($('#usual').val());

        let data = material * max * usual;

        if (isNaN(data) || data <= 0) {
            toastr.error('Ingrese todos los campos');
            return false;
        }

        let dataStock = new FormData(formCreateStock);
        dataStock.append('idMaterial', material);

        if (idStock != '' || idStock != null)
            dataStock.append('idStock', idStock);

        let resp = await sendDataPOST(url, dataStock);

        message(resp);
    }

    /* Eliminar proceso 

    deleteFunction = () => {
        let row = $(this.activeElement).parent().parent()[0];
        let data = tblStock.fnGetData(row);

        // // let id_Stock = data.id_Stock;

        bootbox.confirm({
            title: 'Eliminar',
            message:
                'Está seguro de eliminar este proceso? Esta acción no se puede reversar.',
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
                    $.get(
                        // `../../api/deletePlanProcess/${id_Stock}`,
                        function (data, textStatus, jqXHR) {
                            message(data);
                        }
                    );
                }
            },
        });
    }; */

    /* Mensaje de exito */

    message = (data) => {
        if (data.success == true) {
            $('.cardImportStock').hide(800);
            $('.cardCreateStock').hide(800);
            $('#formCreateStock').trigger('reset');
            updateTable();
            toastr.success(data.message);
            return false;
        } else if (data.error == true) toastr.error(data.message);
        else if (data.info == true) toastr.info(data.message);
    };

    /* Actualizar tabla */

    function updateTable() {
        $('#tblStock').DataTable().clear();
        $('#tblStock').DataTable().ajax.reload();
    }
});
