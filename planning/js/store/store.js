$(document).ready(function () {
    sessionStorage.removeItem('idMaterial');

    $('.cardAddDelivery').hide();

    $('#btnDelivery').click(function (e) { 
        e.preventDefault();
        
        $('#formAddDelivery').trigger('reset');
        $('.cardAddDelivery').toggle(800);
    });

    $(document).on('click', '.deliver', function () {
        let row = $(this).parent().parent()[0];
        let data = tblStore.fnGetData(row);

        sessionStorage.setItem('idMaterial', data.id_material);
        $('#deliverMaterial').modal('show');
    });

    $('.btnCloseDeliver').click(function (e) { 
        e.preventDefault();
        
        sessionStorage.removeItem('idMaterial');
        $('#formDeliverMaterial').trigger('reset');
        $('#deliverMaterial').modal('hide');
    });

    $('#btnSaveDeliver').click(function (e) {
        e.preventDefault();
        
        let email = $('#email').val();
        let password = $('#password').val();

        if (!email || !password) {
            toastr.error('Ingrese los datos');
            return false;
        }

        let dataStore = {};
        dataStore['idMaterial'] = sessionStorage.getItem('idMaterial');
        dataStore['email'] = email;
        dataStore['password'] = password;

        $.ajax({
            type: "POST",
            url: '/api/deliverStore',
            data: dataStore,
            success: function (resp) {
                if (resp.success == true) {
                    sessionStorage.removeItem('idMaterial');
                    $('#formDeliverMaterial').trigger('reset');
                    $('#deliverMaterial').modal('hide');
                    toastr.success(resp.message);
                    $('#tblStore').DataTable().clear();
                    $('#tblStore').DataTable().ajax.reload();
                    return false;
                } else if (resp.error == true) toastr.error(resp.message);
                else if (resp.info == true) toastr.info(data.message);
            }
        });
    });
});