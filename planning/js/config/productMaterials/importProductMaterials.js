$(document).ready(function () {
  let selectedFile;

  $('.cardImport').hide();

  $('#btnImportProduct').on('click', function () {
    $('.cardAddMaterials').hide(800);
    $('.cardImport').toggle(800);
  });

  $('#file').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImport').click(function (e) {
    e.preventDefault();

    file = $('#file').val();
    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    importFile(selectedFile)
      .then((data) => {
        let dataToImport = data.map((item) => {
          return {
            referenceProduct: item.referencia_producto,
            product: item.producto,
            refRawMaterial: item.referencia_material,
            nameRawMaterial: item.material,
            quantity: item.cantidad,
          }  
        }); 

        checkData(dataToImport, url);
      })
      .catch(() => {
        console.log('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  checkData = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/productsMaterialsDataValidation',
      data: { importProducts: data },
      success: function (resp) {
        if (resp.error == true) {
          $('#formImport').trigger('reset');
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
              saveProduct(data);
            } else $('#file').val('');
          },
        });
      },
    });
  };

  saveProduct = (data) => {
    // console.log(data);
    $.ajax({
      type: 'POST',
      url: '/api/addProductsMaterials',
      data: { importProducts: data },
      success: function (r) {
        message(r);
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImports').click(function (e) {
    e.preventDefault();
    link = document.createElement('a');
    link.target = '_blank';

    link.href = 'assets/formatsXlsx/Productos_Materias.xlsx';
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
