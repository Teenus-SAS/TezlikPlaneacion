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

    $('.cardBottons').hide();
    
    let form = document.getElementById('formMaterials');
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
        const expectedHeaders = ['referencia', 'material', 'magnitud', 'unidad', 'existencia', 'gramaje'];
        const actualHeaders = Object.keys(data[0]);

        if (flag_products_measure == '0') {
          expectedHeaders.splice(5, 1); 
        }

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileMaterials').val('');
          toastr.error('Archivo no corresponde con el formato. Verifique nuevamente');
          return false;
        }

        let materialsToImport = data.map((item) => {
          !item.referencia ? item.referencia = '' : item.referencia;
          !item.material ? item.material = '' : item.material; 
          !item.magnitud ? item.magnitud = '' : item.magnitud;
          !item.unidad ? item.unidad = '' : item.unidad;
          !item.existencia ? item.existencia = '' : item.existencia;
          !item.gramaje ? item.gramaje = '' : item.gramaje;
 
          return {
            refRawMaterial: item.referencia,
            nameRawMaterial: item.material, 
            magnitude: item.magnitud,
            unit: item.unidad,
            quantity: item.existencia,
            grammage: item.gramaje
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
  const checkRawMaterial = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/invMaterialsDataValidation',
      data: { importMaterials: data },
      success: function (resp) {
        let arr = resp.import;

        if (arr.length > 0 && arr.error == true) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileMaterials').val('');
          $('#formImportMaterials').trigger('reset');
          toastr.error(resp.message);
          return false;
        }

        if (resp.debugg.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileMaterials').val('');

          // Generar el HTML para cada mensaje
          let concatenatedMessages = resp.debugg.map(item =>
            `<li>
              <span class="badge text-danger" style="font-size: 16px;">${item.message}</span>
            </li>`
          ).join('');

          // Mostramos el mensaje con Bootbox
          bootbox.alert({
            title: 'Estado Importación Data',
            message: `
            <div class="container">
              <div class="col-12">
                <ul>
                  ${concatenatedMessages}
                </ul>
              </div> 
            </div>`,
            size: 'large',
            backdrop: true
          });
          return false;
        }

        if (typeof arr === 'object' && !Array.isArray(arr) && arr !== null && resp.debugg.length == 0) {
          bootbox.confirm({
            title: '¿Desea continuar con la importación?',
            message: `Se han encontrado los siguientes registros:<br><br>Datos a insertar: ${arr.insert} <br>Datos a actualizar: ${arr.update}`,
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
                saveMaterialTable(data);
              } else {
                $('.cardLoading').remove();
                $('.cardBottons').show(400);
                $('#fileMaterials').val('');
              }
            },
          });
        }
      },
    });
  };

  const saveMaterialTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/addInvMaterials',
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
  $('#btnDownloadImportsMaterials').click(async function (e) {
    e.preventDefault();
    $('.cardBottons').hide();
    
    let form = document.getElementById('formMaterials');
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

    namexlsx = 'Inventario_materia_prima.xlsx'; 

    let dataMaterials = JSON.parse(sessionStorage.getItem('dataMaterials'));

    if (dataMaterials.length > 0) {
      for (i = 0; i < dataMaterials.length; i++) {
        if (flag_products_measure == '1')
          data.push({
            referencia: dataMaterials[i].reference,
            material: dataMaterials[i].material, 
            magnitud: dataMaterials[i].magnitude,
            unidad: dataMaterials[i].unit,
            existencia: dataMaterials[i].quantity,
            gramaje: dataMaterials[i].grammage,
          });
        else
          data.push({
            referencia: dataMaterials[i].reference,
            material: dataMaterials[i].material,
            magnitud: dataMaterials[i].magnitude,
            unidad: dataMaterials[i].unit,
            existencia: dataMaterials[i].quantity,
          });
      }

      let ws = XLSX.utils.json_to_sheet(data);
      XLSX.utils.book_append_sheet(wb, ws, 'Materiales');
      XLSX.writeFile(wb, namexlsx);
    } else {
      if (flag_products_measure == '1')
        url = 'assets/formatsXlsx/Inventario_materia_prima(bolsas).xlsx';
      else
        url = 'assets/formatsXlsx/Inventario_materia_prima.xlsx';

      let newFileName = 'Inventario_materia_prima.xlsx';

      fetch(url)
        .then(response => response.blob())
        .then(blob => {
          let link = document.createElement('a');
          link.href = URL.createObjectURL(blob);
          link.download = newFileName;

          document.body.appendChild(link);
          link.click();

          document.body.removeChild(link);
          URL.revokeObjectURL(link.href); // liberar memoria
        })
        .catch(console.error);
    }

    $('.cardLoading').remove();
    $('.cardBottons').show(400);
    $('#fileMaterials').val('');
  });
});
