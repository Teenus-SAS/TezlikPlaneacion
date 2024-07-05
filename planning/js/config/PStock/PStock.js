$(document).ready(function () {
    // loadClients(2);
    /* Ocultar panel crear stock */

    $('.cardCreatePStock').hide();

    /* Abrir panel crear stock */

    $('#btnNewPStock').click(function (e) {
        e.preventDefault();

        $('.cardImportPStock').hide(800);
        $('.cardCreatePStock').toggle(800);
        $('#formCreatePStock').trigger('reset');
        $('#btnCreatePStock').html('Crear');

        sessionStorage.removeItem('idStock');
    });

    /* Crear nuevo proceso */

    $('#btnCreatePStock').click(function (e) {
        e.preventDefault();

        let idStock = sessionStorage.getItem('idStock');
        if (!idStock)
            checkDataPStock('/api/addPStock', idStock);
        else
            checkDataPStock('/api/updatePStock', idStock);
    });

    /* Actualizar procesos */

    $(document).on('click', '.updatePStock', function (e) {
        $('.cardImportPStock').hide(800);
        $('.cardCreatePStock').show(800);
        $('#btnCreatePStock').html('Actualizar');

        let row = $(this).parent().parent()[0];
        let data = tblPStock.fnGetData(row);

        sessionStorage.setItem('idStock', data.id_stock_product);
        $(`#refProduct option[value=${data.id_product}]`).prop('selected', true);
        $(`#selectNameProduct option[value=${data.id_product}]`).prop('selected', true);
        $('#pMax').val(data.max_term);
        $('#pUsual').val(data.usual_term);

        $('html, body').animate(
            {
                scrollTop: 0,
            },
            1000
        );
    });

    const checkDataPStock = async (url, idStock) => {
        let id_product = parseFloat($('#refProduct').val()); 
        let max = parseFloat($('#pMax').val());
        let usual = parseFloat($('#pUsual').val());

        let data = id_product * max * usual;

        if (isNaN(data) || data <= 0) {
            toastr.error('Ingrese todos los campos');
            return false;
        }

        let dataStock = new FormData(formCreatePStock);
        dataStock.append('idProduct', id_product);

        if (idStock != '' || idStock != null)
            dataStock.append('idStock', idStock);

        let resp = await sendDataPOST(url, dataStock);

        messagePS(resp);
    }

    /* Eliminar proceso 

    deleteFunction = () => {
        let row = $(this.activeElement).parent().parent()[0];
        let data = tblPStock.fnGetData(row);

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
                if (result) {
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

    messagePS = (data) => {
        if (data.success == true) {
            $('.cardImportPStock').hide(800);
            $('.cardCreatePStock').hide(800);
            $('#formCreatePStock').trigger('reset');
            updateTable();
            toastr.success(data.message);
            return false;
        } else if (data.error == true) toastr.error(data.message);
        else if (data.info == true) toastr.info(data.message);
    };

    /* Actualizar tabla */

    function updateTable() {
        $('#tblPStock').DataTable().clear();
        $('#tblPStock').DataTable().ajax.reload();
    }
});
