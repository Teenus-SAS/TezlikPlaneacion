$(document).ready(function () {
    sessionStorage.removeItem('idMaterial');
    sessionStorage.removeItem('stored');
    sessionStorage.removeItem('pending');
    sessionStorage.removeItem('delivered');

    $('.cardAddDelivery').hide();

    $('#btnDelivery').click(function (e) {
        e.preventDefault();
        
        $('#formAddDelivery').trigger('reset');
        $('.cardAddDelivery').toggle(800);
    });

    $(document).on('click', '.deliver', function () {
        let row = $(this).parent().parent()[0];
        let data = tblStore.fnGetData(row); 
        let id_material = data.id_material;
        let quantity = data.quantity;
        let reserved = data.reserved;

        bootbox.confirm({
            title: 'Entrega Material',
            message:
                `<div class="col-sm-6 floating-label enable-floating-label show-label">
                    <label for="">Cantidad a Entregar</label>
                    <input type="number" class="form-control text-center" id="quantity" name="quantity">
                </div>`,
            buttons: {
                confirm: {
                    label: 'Guardar',
                    className: 'btn-success',
                },
                cancel: {
                    label: 'Cancelar',
                    className: 'btn-danger',
                },
            },
            callback: function (result) {
                if (result == true) {
                    let store = parseFloat($('#quantity').val());

                    if (!store || store <= 0) {
                        toastr.error('Ingrese todos los campos');
                        return false;
                    }

                    store <= reserved ? pending = (reserved - store) : pending = 0;

                    sessionStorage.setItem('idMaterial', id_material);
                    sessionStorage.setItem('stored', (quantity - store));
                    sessionStorage.setItem('pending', pending);
                    sessionStorage.setItem('delivered', store);
                    $('#deliverMaterial').modal('show');
                }
            },
        });


        
    });

    $('.btnCloseDeliver').click(function (e) {
        e.preventDefault();
        
        sessionStorage.removeItem('idMaterial');
        sessionStorage.removeItem('stored');
        sessionStorage.removeItem('pending');
        sessionStorage.removeItem('delivered');

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
        dataStore['stored'] = sessionStorage.getItem('stored');
        dataStore['pending'] = sessionStorage.getItem('pending');
        dataStore['delivered'] = sessionStorage.getItem('delivered');
        dataStore['email'] = email;
        dataStore['password'] = password;

        $.ajax({
            type: "POST",
            url: '/api/deliverStore',
            data: dataStore,
            success: function (resp) {
                message(resp);
            }
        });
    });

    message = (data) => {
        if (data.success == true) {
            sessionStorage.removeItem('idMaterial');
            sessionStorage.removeItem('stored');
            sessionStorage.removeItem('pending');
            sessionStorage.removeItem('delivered');
            
            $('#formDeliverMaterial').trigger('reset');
            $('#deliverMaterial').modal('hide');
            toastr.success(data.message);
            $('#tblStore').DataTable().clear();
            $('#tblStore').DataTable().ajax.reload();
            return false;
        } else if (data.error == true) toastr.error(data.message);
        else if (data.info == true) toastr.info(data.message);
    }
});