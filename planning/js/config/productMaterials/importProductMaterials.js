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
          id = sessionStorage.getItem('id');
          // if (id == 1)
            arr = {
              referenceProduct: item.referencia_producto,
              product: item.producto,
              refRawMaterial: item.referencia_material,
              nameRawMaterial: item.material,
              quantity: item.cantidad,
            };

          // if (id == 2)
          //   arr = {
          //     referenceFinalProduct: item.referencia_producto_final,
          //     finalProduct: item.producto_final,
          //     referenceProduct: item.referencia,
          //     product: item.producto,
          //   };

          return arr;
        });

        // if (id == 1)
          url = {
            validation: '/api/productsMaterialsDataValidation',
            save: '/api/addProductsMaterials',
          };
        // if (id == 2)
        //   url = {
        //     validation: '/api/productsInProcessDataValidation',
        //     save: '/api/addProductInProcess',
        //   };

        checkData(dataToImport, url);
      })
      .catch(() => {
        console.log('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  checkData = (data, url) => {
    $.ajax({
      type: 'POST',
      url: url.validation,
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
              saveProduct(data, url);
            } else $('#file').val('');
          },
        });
      },
    });
  };

  saveProduct = (data, url) => {
    // console.log(data);
    $.ajax({
      type: 'POST',
      url: url.save,
      data: { importProducts: data },
      success: function (r) {
        console.log(r);
        /* Mensaje de exito */
        if (r.success == true) {
          $('.cardImport').hide(800);
          $('#formImport').trigger('reset');

          updateTable();

          toastr.success(r.message);
          return false;
        } else if (r.error == true) {
          $('#file').val('');
          toastr.error(r.message);
        } else if (r.info == true) {
          $('#file').val('');
          toastr.info(r.message);
        }

        /* Actualizar tabla */
        function updateTable() {
          $('#tblConfigMaterials').DataTable().clear();
          $('#tblConfigMaterials').DataTable().ajax.reload();

          // $('#tblProductsInProcess').DataTable().clear();
          // $('#tblProductsInProcess').DataTable().ajax.reload();
        }
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImports').click(function (e) {
    e.preventDefault();

    // id = sessionStorage.getItem('id');

    // if (id == 1)
      url = 'assets/formatsXlsx/Productos_Materias.xlsx';
    // if (id == 2) url = 'assets/formatsXlsx/Productos_En_Proceso.xlsx';

    link = document.createElement('a');
    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
