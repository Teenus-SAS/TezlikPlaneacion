$(document).ready(function () {
  /* Ocultar panel crear producto */

  $('.cardInventoryABC').hide();

  $('#btnNewInventoryABC').click(function (e) { 
    e.preventDefault();
    
    $('.cardInventoryABC').toggle();
    $('#formInventoryABC').trigger('reset');
  });

  /* Crear nuevo categoria */
  $('#btnSaveInventoryABC').click(function (e) {
    e.preventDefault();
      
    let idInventory = sessionStorage.getItem('id_inventory');
    
    if (!idInventory) {
      checkDataInventory('/api/addInventoryABC', idInventory);
    } else {
      checkDataInventory('/api/updateInventoryABC', idInventory);
    }
  });

  /* Actualizar categoria */
  $(document).on('click', '.updateInventory', function (e) {
    $('.cardInventoryABC').show(800);
    $('.cardImportInventory').hide(800);
    $('.cardAddMonths').hide(800);
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

  const checkDataInventory = async (url, idInventory) => {
    let a = parseInt($('#a').val());
    let b = parseInt($('#b').val());
    let c = parseInt($('#c').val());

    let arr = a * b;

    if (!arr || isNaN(arr) || arr <= 0) {
      toastr.error('Ingrese todos los campos');
      return false;
    }

    if (a > 100 || b > 100 || c > 100) {
      toastr.error('El procentaje debe ser menor a 100');
      return false;
    } 

    if (a < b || a < c || b < c) {
      toastr.error('Verificar que los valores B y C no sean mayores a A');
      return false;
    }

    let dataInventory = new FormData(formInventoryABC);

    if (idInventory != "" || idInventory != null) {
      dataInventory.append("idInventory", idInventory);
    }

    let resp = await sendDataPOST(url, dataInventory);

    message(resp);
  };

  /* Mensaje de exito */
  message = (data) => {
    if (data.success == true) {
      $('#btnNewInventoryABC').hide();
      $('.cardInventoryABC').hide(800);
      $('#formInventoryABC').trigger('reset');
      loadAllData();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  }; 
});
