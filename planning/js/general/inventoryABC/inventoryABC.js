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
    checkDataInventory('/api/updateInventoryABC'); 
  });

  /* Actualizar categoria 
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
  }); */

  const checkDataInventory = async (url) => {
    let a = parseInt($('#a').val());
    let b = parseInt($('#b').val());
    let c = parseInt($('#c').val());

    let data = JSON.parse(sessionStorage.getItem('dataInventoryABC'));

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
 
    dataInventory.append("idInventory", data[0].id_inventory); 

    let resp = await sendDataPOST(url, dataInventory);

    let cantMonths = $('#cantMonths').val();

    if (cantMonths) { 
      await searchData(`/api/classification/${cantMonths}`);
    };

    messageInventory(resp);
  };

  /* Mensaje de exito 
  const message = (data) => {
    if (data.success == true) {
      $('#btnNewInventoryABC').hide();
      $('.cardInventoryABC').hide(800);
      $('#formInventoryABC').trigger('reset');
      loadAllData();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  }; */
});
