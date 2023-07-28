$(document).ready(function () {
    $('.cardAddDate').hide();

    $('#btnNewDate').click(function (e) { 
        e.preventDefault();

        $('.cardAddDate').toggle(800);
        $('#formAddDate').trigger('reset');
    });

    $('#btnAddDate').click(async function (e) { 
        e.preventDefault();
        
        let order = $('#order').val();
        let date = $('#date').val();
                
        if (!order || !date) {
            toastr.error('Ingrese los campos');
            return false;
        }

        let form = new FormData();
        form.append('idOrder', order);
        form.append('date', date);

        let resp = await sendDataPOST('/api/changeOffices', form);
        message(resp); 
    });

    /* Mensaje de exito */
    message = (data) => {
        if (data.success == true) {
            updateTable();
            $('.cardAddDate').hide(800);
            $('#formAddDate').trigger('reset');
            toastr.success(data.message);
            return false;
        } else if (data.error == true) toastr.error(data.message);
        else if (data.info == true) toastr.info(data.message);
    };

    /* Actualizar tabla */
    function updateTable() {
        $('#tblOffices').DataTable().clear();
        $('#tblOffices').DataTable().ajax.reload();
    }
});