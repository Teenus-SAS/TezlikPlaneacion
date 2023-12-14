$(document).ready(function () {
  // Ocultar card formulario Analisis Inventario ABC
  $('.cardAddMonths').hide();

  // Ocultar botón analisar Inventario ABC
  $('.cardBtnAddMonths').hide();

  $('#btnInvetoryABC').click(function (e) {
    e.preventDefault();
    $('.cardImportInventory').hide(800);
    $('.cardAddMonths').toggle(800);

    $('#formAddMonths').trigger('reset');
  });

  $('#btnAddMonths').click(function (e) {
    e.preventDefault();

    cantMonths = $('#cantMonths').val();

    if (!cantMonths || cantMonths == '') {
      toastr.error('Ingrese cantidad a calcular');
      return false;
    }

    // category = $('#category').val();

    // products = sessionStorage.getItem('dataProducts');

    // products = JSON.parse(products);
    // dataInventory = [];
    // // Almacenar data para calcular clasificacion
    // for (let i in products) {
    //   dataInventory.push({
    //     cantMonths: cantMonths,
    //     idProduct: products[i]['id_product'],
    //   });
    // }

    // $.ajax({
    //   type: 'POST',
    //   url: '/api/calcClassification',
    //   data: { products: dataInventory },
    //   success: function (response) {
    //     message(response);
    //   },
    // });
    $.get(`/api/classification/${cantMonths}`, 
      function (data, textStatus, jqXHR) {
        message(data);
      },
    );
  });

  /* Mensaje de exito */
  message = async (data) => {
    if (data.success == true) {
      $('.cardImportInventory').hide(800);
      $('.cardAddMonths').hide(800);
      $('#formAddMonths').trigger('reset');

      await loadInventory();
      $('#category').val(1).trigger('change');

      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };
});
