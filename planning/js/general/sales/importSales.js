$(document).ready(function () {
  let selectedFile;

  $('.cardImportSales').hide();

  $('#btnImportNewSales').click(function (e) {
    e.preventDefault();
    $('.cardImportSales').toggle(800);
  });

  $('#fileSales').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportSales').click(function (e) {
    e.preventDefault();

    file = $('#fileSales').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    importFile(selectedFile)
      .then((data) => {
        let SalesToImport = data.map((item) => {
          !item.enero ? (item.enero = '') : item.enero;
          !item.febrero ? (item.febrero = '') : item.febrero;
          !item.marzo ? (item.marzo = '') : item.marzo;
          !item.abril ? (item.abril = '') : item.abril;
          !item.mayo ? (item.mayo = '') : item.mayo;
          !item.junio ? (item.junio = '') : item.junio;
          !item.julio ? (item.julio = '') : item.julio;
          !item.agosto ? (item.agosto = '') : item.agosto;
          !item.septiembre ? (item.septiembre = '') : item.septiembre;
          !item.octubre ? (item.octubre = '') : item.octubre;
          !item.noviembre ? (item.noviembre = '') : item.noviembre;
          !item.diciembre ? (item.diciembre = '') : item.diciembre;

          return {
            referenceProduct: item.referencia,
            product: item.producto,
            january: item.enero,
            february: item.febrero,
            march: item.marzo,
            april: item.abril,
            may: item.mayo,
            june: item.junio,
            july: item.julio,
            august: item.agosto,
            september: item.septiembre,
            october: item.octubre,
            november: item.noviembre,
            december: item.diciembre,
          };
        });
        checkSales(SalesToImport);
      })
      .catch(() => {
        console.log('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  checkSales = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/unitSalesDataValidation',
      data: { importUnitSales: data },
      success: function (resp) {
        if (resp.error == true) {
          $('#formImportSales').trigger('reset');
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
              saveSalesTable(data);
            } else $('#fileSales').val('');
          },
        });
      },
    });
  };

  saveSalesTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '../../api/addUnitSales',
      data: { importUnitSales: data },
      success: function (r) {
        /* Mensaje de exito */
        if (r.success == true) {
          $('.cardImportSales').hide(800);
          $('#formImportSales').trigger('reset');
          updateTable();
          toastr.success(r.message);
          return false;
        } else if (r.error == true) toastr.error(r.message);
        else if (r.info == true) toastr.info(r.message);

        /* Actualizar tabla */
        function updateTable() {
          $('#tblSales').DataTable().clear();
          $('#tblSales').DataTable().ajax.reload();
        }
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsSales').click(function (e) {
    e.preventDefault();

    url = 'assets/formatsXlsx/Unidades_Ventas.xlsx';

    link = document.createElement('a');

    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
