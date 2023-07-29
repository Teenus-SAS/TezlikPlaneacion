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

    $(document).on('change', '.dateOrders', async function (e) {
        e.preventDefault();

        let dateOrders = document.getElementsByClassName('dateOrders');
        let status = true;

        for (let i = 0; i < dateOrders.length; i++) {
            if (dateOrders[i].value == '') {
                status = false;
                break;
            }
        }

        if (status == false) {
            toastr.error('Ingrese Fecha Inicial y Fecha Final');
        } else {
            loadTblOffices(dateOrders[0].value, dateOrders[1].value);
        }
    });

    loadDateOrders = () => {
        let date = new Date().toISOString().split('T')[0];

        $('#lastDate').val(date);

        let maxDate = document.getElementById('lastDate');
        let minDate = document.getElementById('firtsDate');

        maxDate.setAttribute("max", date);
        minDate.setAttribute("max", date);
    }

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

    loadDateOrders();
});