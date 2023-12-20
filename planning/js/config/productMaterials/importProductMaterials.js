$(document).ready(function () {

  $('.cardImport').hide();

  $('#btnImportProduct').on('click', function () {
    $('.cardAddMaterials').hide(800);
    $('.cardImport').toggle(800);
  });

  $('#btnImportProductsMaterials').click(function (e) {
    e.preventDefault();
 
    const fileInput = document.getElementById('fileProductsMaterials');
    const selectedFile = fileInput.files[0];
    
    if (!fileProductsMaterials) {
      toastr.error('Seleccione un archivo');
      return false;
    }
    
    $('.cardBottons').hide();
    
    let form = document.getElementById('formProductMaterial');
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
        const expectedHeaders = ['referencia_producto','producto','referencia_material','material','magnitud','unidad','cantidad'];
        const actualHeaders = Object.keys(data[0]);

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileProductsMaterials').val('');
          toastr.error('Archivo no corresponde a el formato. Verifique nuevamente');
          return false;
        }

        let dataToImport = data.map((item) => {
          return {
            referenceProduct: item.referencia_producto,
            product: item.producto,
            refRawMaterial: item.referencia_material,
            nameRawMaterial: item.material,
            magnitude: item.magnitud,
            unit: item.unidad,
            quantity: item.cantidad,
          }
        });

        checkData(dataToImport);
      })
      .catch(() => {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileProductsMaterials').val('');

        toastr.error('Ocurrio un error. Intente Nuevamente');
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
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileProductsMaterials').val('');

          $('#formImportProductMaterial').trigger('reset');
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
            } else {
              $('.cardLoading').remove();
              $('.cardBottons').show(400);
              $('#fileProductsMaterials').val('');
            }
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
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileProductsMaterials').val('');

        messageMaterial(r);
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsProductsMaterials').click(function (e) {
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
