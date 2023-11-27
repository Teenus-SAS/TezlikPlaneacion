$(document).ready(function () {
    $('.cardAddDelivery').hide();

    $('#btnDelivery').click(function (e) { 
        e.preventDefault();
        
        $('#formAddDelivery').trigger('reset');
        $('.cardAddDelivery').toggle(800);
    });

    $('#btnAddDelivery').click(function (e) { 
        e.preventDefault();
        
        let quantity = $('#quantity').val();

        if (!quantity) {
            toastr.error('Ingrese cantidad');
            return false;
        }
    });
});