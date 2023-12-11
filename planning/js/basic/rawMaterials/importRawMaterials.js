$(document).ready(function () {
  let selectedFile;

  $('.cardImportMaterials').hide();

  $('#btnImportNewMaterials').click(function (e) {
    e.preventDefault();
    $('.cardRawMaterials').hide(800);
    $('.cardImportMaterials').toggle(800);
  });

  $('#fileMaterials').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportMaterials').click(function (e) {
    e.preventDefault();

    file = $('#fileMaterials').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    importFile(selectedFile)
      .then((data) => {
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
        console.log('Ocurrio un error. Intente Nuevamente');
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
            } else $('#fileMaterials').val('');
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
        /* Mensaje de exito */
        if (r.success == true) {
          $('.cardImportMaterials').hide(800);
          $('#formImportMaterials').trigger('reset');
          updateTable();
          toastr.success(r.message);
          return false;
        } else if (r.error == true) toastr.error(r.message);
        else if (r.info == true) toastr.info(r.message);

        /* Actualizar tabla */
        function updateTable() {
          $('#tblRawMaterials').DataTable().clear();
          $('#tblRawMaterials').DataTable().ajax.reload();
        }
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsMaterials').click(async function (e) {
    e.preventDefault();

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
  });
});
