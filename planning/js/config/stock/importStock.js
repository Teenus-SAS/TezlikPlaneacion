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
        console.log('Ocurrio un error. Intente Nuevamente');
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
            } else $('#fileStock').val('');
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
        message(r);
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsStock').click(async function (e) {
    e.preventDefault();

    let wb = XLSX.utils.book_new();

    let data = [];

    namexlsx = 'Stock.xlsx';
    url = '/api/materials';
    
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
  });
});
