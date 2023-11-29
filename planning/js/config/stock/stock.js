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

        // sessionStorage.removeItem('id_process');
    });

    /* Crear nuevo proceso */

    $('#btnCreateStock').click(function (e) {
        e.preventDefault();

         
    });

    /* Actualizar procesos 

    $(document).on('click', '.updateProcess', function (e) {
        $('.cardImportStock').hide(800);
        $('.cardCreateStock').show(800);
        $('#btnCreateStock').html('Actualizar');

        let row = $(this).parent().parent()[0];
        let data = tblProcess.fnGetData(row);

        // // sessionStorage.setItem('id_process', data.id_process);
        $('#process').val(data.process);

        $('html, body').animate(
            {
                scrollTop: 0,
            },
            1000
        );
    }); 

    updateProcess = () => {
        let data = $('#formCreateStock').serialize();
        // idProcess = sessionStorage.getItem('id_process');
        data = data + '&idProcess=' + idProcess;

        $.post(
            '../../api/updatePlanProcess',
            data,
            function (data, textStatus, jqXHR) {
                message(data);
            }
        );
    }; */

    /* Eliminar proceso 

    deleteFunction = () => {
        let row = $(this.activeElement).parent().parent()[0];
        let data = tblProcess.fnGetData(row);

        // // let id_process = data.id_process;

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
                        // `../../api/deletePlanProcess/${id_process}`,
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
