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
});
