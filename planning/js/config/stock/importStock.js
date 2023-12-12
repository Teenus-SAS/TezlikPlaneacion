$(document).ready(function () {
  let selectedFile;

  $('.cardImportStock').hide();

  $('#btnImportNewStock').click(function (e) {
    e.preventDefault();
    $('.cardCreateStock').hide(800);
    $('.cardImportStock').toggle(800);
  });

  $('#fileStock').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportStock').click(function (e) {
    e.preventDefault();

    file = $('#fileStock').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    $('.cardBottons').hide();

    let form = document.getElementById('formStock');
    form.insertAdjacentHTML(
      'beforeend',
      `<div class="col-sm-1 cardLoading" style="margin-top: 7px; margin-left: 15px">
        <div class="spinner-border text-secondary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
      </div>`
    );

    importFile(selectedFile)
      .then((data) => {
        let StockToImport = data.map((item) => {
          return {
            refRawMaterial: item.referencia_material,
            nameRawMaterial: item.material,
            max: item.plazo_maximo,
            usual: item.plazo_habitual,
          };
        });
        checkStock(StockToImport);
      })
      .catch(() => {
                  $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileStock').val('');

        toastr.error('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  checkStock = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/stockDataValidation',
      data: { importStock: data },
      success: function (resp) {
        if (resp.error == true) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileStock').val('');

          $('#formImportStock').trigger('reset');
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
            if (result == true) {
              saveStockTable(data);
            } else {
                        $('.cardLoading').remove();
          $('.cardBottons').show(400);
$('#fileStock').val('');
            }
          },
        });
      },
    });
  };

  saveStockTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '../../api/addStock',
      data: { importStock: data },
      success: function (r) {
        $('.cardLoading').remove();
          $('.cardBottons').show(400);
        $('#fileStock').val('');
        
        message(r);
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsStock').click(async function (e) {
    e.preventDefault();

    $('.cardBottons').hide();

    let form = document.getElementById('formStock');
    form.insertAdjacentHTML(
      'beforeend',
      `<div class="col-sm-1 cardLoading" style="margin-top: 7px; margin-left: 15px">
        <div class="spinner-border text-secondary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
      </div>`
    );

    let wb = XLSX.utils.book_new();

    let data = [];

    namexlsx = 'Stock.xlsx';
    url = '/api/stockMaterials';
    
    let stock = await searchData(url);

    if (stock.length > 0) {
      for (i = 0; i < stock.length; i++) {
        data.push({
          referencia_material: stock[i].reference,
          material: stock[i].material,
          plazo_maximo: parseFloat(stock[i].max_term),
          plazo_habitual: parseFloat(stock[i].usual_term),
        });
      }

      let ws = XLSX.utils.json_to_sheet(data);
      XLSX.utils.book_append_sheet(wb, ws, 'Stock');
      XLSX.writeFile(wb, namexlsx);
    }

    $('.cardLoading').remove();
    $('.cardBottons').show(400);
    $('#fileStock').val('');
  });
});
