$(document).ready(function () {
  let selectedFile;

  $('.cardImportMaterials').hide();

  $(document).on('click', '#btnImportNewMaterials', function () {
    $('.cardRawMaterials').hide(800);
    $('.cardImportMaterials').toggle(800);
  });

  $(document).on('change', '#fileMaterials', function () {
    selectedFile = e.target.files[0];
  });

  $(document).on('click', '#btnImportMaterials', function () {
    file = $('#fileMaterials').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    $('.cardBottons').hide();
    
    let form = document.getElementById('formMaterials');
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
        const expectedHeaders = ['referencia', 'material', 'magnitud', 'unidad', 'existencia'];
        const actualHeaders = Object.keys(data[0]);

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileMaterials').val('');
          toastr.error('Archivo no corresponde a el formato. Verifique nuevamente');
          return false;
        }

        let materialsToImport = data.map((item) => {
          return {
            refRawMaterial: item.referencia,
            nameRawMaterial: item.material,
            magnitude: item.magnitud,
            unit: item.unidad,
            quantity: item.existencia,
          };
        });

        checkRawMaterial(materialsToImport);
      })
      .catch(() => {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileMaterials').val('');
        toastr.error('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  checkRawMaterial = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/materialsDataValidation',
      data: { importMaterials: data },
      success: function (resp) {
        if (resp.error == true) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileMaterials').val('');
          $('#formImportMaterials').trigger('reset');
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
              saveMaterialTable(data);
            } else {
              $('.cardLoading').remove();
              $('.cardBottons').show(400);
              $('#fileMaterials').val('');
            }
          },
        });
      },
    });
  };

  saveMaterialTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '../api/addMaterials',
      data: { importMaterials: data },
      success: function (r) {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileMaterials').val('');

        messageMaterials(r);
      },
    });
  };

  /* Descargar formato */
  $(document).on('click', '#btnDownloadImportsMaterials', async function () {
    $('.cardBottons').hide();
    
    let form = document.getElementById('formMaterials');
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

    namexlsx = 'Materia_prima.xlsx';
    url = '/api/materials';
    
    let materials = await searchData(url);

    if (materials.length > 0) {
      for (i = 0; i < materials.length; i++) {
        data.push({
          referencia: materials[i].reference,
          material: materials[i].material,
          magnitud: materials[i].magnitude,
          unidad: materials[i].unit,
          existencia: materials[i].quantity,
        });
      }

      let ws = XLSX.utils.json_to_sheet(data);
      XLSX.utils.book_append_sheet(wb, ws, 'Materiales');
      XLSX.writeFile(wb, namexlsx);
    }

    $('.cardLoading').remove();
    $('.cardBottons').show(400);
    $('#fileMaterials').val('');
  });
});
