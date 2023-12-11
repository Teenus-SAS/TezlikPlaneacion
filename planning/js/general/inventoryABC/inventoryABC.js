$(document).ready(function () {
  /* Ocultar panel crear producto */

  $('.cardInventoryABC').hide();

  /* Crear nuevo categoria */

    $('#btnSaveInventoryABC').click(function (e) {
        e.preventDefault();
      
        let a = $('#a').val();
        let b = $('#b').val();
        let c = $('#c').val();

        let arr = a * b * c;

        if (!arr || isNaN(arr) || arr <= 0) {
            toastr.error('Ingrese todos los campos');
            return false;
        }

        if (a > 100||b>100||c>100) {
            toastr.error('El procentaje debe ser menor a 100');
            return false;
        }

        let data = $('#formInventoryABC').serialize();
        idInventory = sessionStorage.getItem('id_inventory');
        data = data + '&idInventory=' + idInventory;

        $.post(
            '../../api/updateInventoryABC',
            data,
            function (data, textStatus, jqXHR) {
                message(data);
            }
        );
    
    });

  /* Actualizar categoria */

    $(document).on('click', '.updateInventory', function (e) {
        $('.cardInventoryABC').show(800);
        $('#btnSaveInventoryABC').html('Actualizar');

        let row = $(this).parent().parent()[0];
        let data = tblInventoryABC.fnGetData(row);

        sessionStorage.setItem('id_inventory', data.id_inventory);
        $('#a').val(data.a);
        $('#b').val(data.b);
        $('#c').val(data.c);
      
        $('html, body').animate(
            {
                scrollTop: 0,
            },
            1000
        );
    });
 

  /* Mensaje de exito */

  message = (data) => {
    if (data.success == true) {
      $('.cardInventoryABC').hide(800);
      $('#formInventoryABC').trigger('reset');
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblInventoryABC').DataTable().clear();
    $('#tblInventoryABC').DataTable().ajax.reload();
  }
});
