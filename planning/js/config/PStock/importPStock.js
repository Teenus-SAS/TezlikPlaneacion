$(document).ready(function () {
  let selectedFile;

  $('.cardImportPStock').hide();

  $('#btnImportNewPStock').click(function (e) {
    e.preventDefault();
    $('.cardCreatePStock').hide(800);
    $('.cardImportPStock').toggle(800);
  });

  $('#filePStock').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportPStock').click(function (e) {
    e.preventDefault();

    let file = $('#filePStock').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    $('.cardBottons').hide();

    let form = document.getElementById('formPStock');
    form.insertAdjacentHTML(
      'beforeend',
      `<div class="col-sm-1 cardLoading" style="margin-top: 7px; margin-left: 15px">
        <div class="spinner-grow text-dark" role="status">
            <span class="sr-only">Loading...</span>
        </div>
      </div>`
    );

    importFile(selectedFile)
      .then((data) => {
        const expectedHeaders = ['referencia_producto', 'producto', 'plazo_minimo', 'plazo_maximo'];
        const actualHeaders = Object.keys(data[0]);

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#filePStock').val('');
          toastr.error('Archivo no corresponde con el formato. Verifique nuevamente');
          return false;
        }

        let stockToImport = data.map((item) => {
          return {
            referenceProduct: item.referencia_producto,
            product: item.producto,
            min: item.plazo_minimo,
            max: item.plazo_maximo,
          };
        });
        checkPStock(stockToImport);
      })
      .catch(() => {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#filePStock').val('');

        toastr.error('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  const checkPStock = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/pStockDataValidation',
      data: { importStock: data },
      success: function (resp) {
        if (resp.error == true) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#filePStock').val('');

          $('#formImportRMStock').trigger('reset');
          toastr.error(resp.message);
          return false;
        }

        bootbox.confirm({
          title: '¿Desea continuar con la importación?',
          message: `Se han encontrado los siguientes registros:<br><br>Datos a insertar: ${resp.insert} <br>Datos a actualizar: ${resp.update}`,
          buttons: {
            confirm: {
              label: 'Si',
              className: 'btn-success',
            },
            cancel: {
              label: 'No',
              className: 'btn-danger',
            },
          },
          callback: function (result) {
            if (result) {
              savePStockTable(data);
            } else {
              $('.cardLoading').remove();
              $('.cardBottons').show(400);
              $('#filePStock').val('');
            }
          },
        });
      },
    });
  };

  const savePStockTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/addPStock',
      data: { importStock: data },
      success: function (r) {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#filePStock').val('');
        
        messagePS(r);
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsPStock').click(async function (e) {
    e.preventDefault();

    $('.cardBottons').hide();

    let form = document.getElementById('formPStock');
    form.insertAdjacentHTML(
      'beforeend',
      `<div class="col-sm-1 cardLoading" style="margin-top: 7px; margin-left: 15px">
        <div class="spinner-grow text-dark" role="status">
            <span class="sr-only">Loading...</span>
        </div>
      </div>`
    );

    let wb = XLSX.utils.book_new();

    let data = [];

    namexlsx = 'Stock_Products.xlsx';
    url = '/api/stockProducts';
    
    let stock = await searchData(url);

    if (stock.length > 0) {
      for (i = 0; i < stock.length; i++) {
        data.push({
          referencia_producto: stock[i].reference,
          producto: stock[i].product,
          plazo_minimo: parseFloat(stock[i].min_term),
          plazo_maximo: parseFloat(stock[i].max_term),
        });
      }

      let ws = XLSX.utils.json_to_sheet(data);
      XLSX.utils.book_append_sheet(wb, ws, 'Stock');
      XLSX.writeFile(wb, namexlsx);
    }

    $('.cardLoading').remove();
    $('.cardBottons').show(400);
    $('#filePStock').val('');
  });
});
