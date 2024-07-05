$(document).ready(function () {
    loadClients(2);

    $('.selectNavigation').click(function (e) {
        e.preventDefault();

        $('.cardGeneralSN').hide();

        if (this.id == 'nProducts') {
            $('.cardProducts').show();
            $('.cardMaterials').hide();
        } else if (this.id == 'nMaterials') {
            $('.cardMaterials').show();
            $('.cardProducts').hide();
        }

        let tables = document.getElementsByClassName(
            'dataTable'
        );

        for (let i = 0; i < tables.length; i++) {
            let attr = tables[i];
            attr.style.width = '100%';
            attr = tables[i].firstElementChild;
            attr.style.width = '100%';
        }
    });
    
    /* Ocultar panel crear stock */

    $('.cardCreateRMStock').hide();

    /* Abrir panel crear stock */

    $('#btnNewRMStock').click(function (e) {
        e.preventDefault();

        $('.cardImportRMStock').hide(800);
        $('.cardCreateRMStock').toggle(800);
        $('#formCreateRMStock').trigger('reset');
        $('#btnCreateRMStock').html('Crear');

        sessionStorage.removeItem('idStock');
    });

    /* Crear nuevo proceso */

    $('#btnCreateRMStock').click(function (e) {
        e.preventDefault();

        let idStock = sessionStorage.getItem('idStock');
        if (!idStock)
            checkDataRMStock('/api/addRMStock', idStock);
        else
            checkDataRMStock('/api/updateRMStock', idStock);
    });

    /* Actualizar procesos */

    $(document).on('click', '.updateRMStock', function (e) {
        $('.cardImportRMStock').hide(800);
        $('.cardCreateRMStock').show(800);
        $('#btnCreateRMStock').html('Actualizar');

        let row = $(this).parent().parent()[0];
        let data = tblRMStock.fnGetData(row);

        sessionStorage.setItem('idStock', data.id_stock_material);
        $(`#material option[value=${data.id_material}]`).prop('selected', true);
        $(`#client option[value=${data.id_provider}]`).prop('selected', true);
        $('#rMMax').val(data.max_term);
        $('#rMUsual').val(data.usual_term);

        $('html, body').animate(
            {
                scrollTop: 0,
            },
            1000
        );
    });

    const checkDataRMStock = async (url, idStock) => {
        let material = parseFloat($('#material').val());
        let provider = parseFloat($('#client').val());
        let max = parseFloat($('#rMMax').val());
        let usual = parseFloat($('#rMUsual').val());

        let data = material * provider * max * usual;

        if (isNaN(data) || data <= 0) {
            toastr.error('Ingrese todos los campos');
            return false;
        }

        let dataStock = new FormData(formCreateRMStock);
        dataStock.append('idMaterial', material);

        if (idStock != '' || idStock != null)
            dataStock.append('idStock', idStock);

        let resp = await sendDataPOST(url, dataStock);

        messageRMS(resp);
    }

    /* Eliminar proceso 

    deleteFunction = () => {
        let row = $(this.activeElement).parent().parent()[0];
        let data = tblRMStock.fnGetData(row);

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

    messageRMS = (data) => {
        if (data.success == true) {
            $('.cardImportRMStock').hide(800);
            $('.cardCreateRMStock').hide(800);
            $('#formCreateRMStock').trigger('reset');
            updateTable();
            toastr.success(data.message);
            return false;
        } else if (data.error == true) toastr.error(data.message);
        else if (data.info == true) toastr.info(data.message);
    };

    /* Actualizar tabla */

    function updateTable() {
        $('#tblRMStock').DataTable().clear();
        $('#tblRMStock').DataTable().ajax.reload();
    }
});
