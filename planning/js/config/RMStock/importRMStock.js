$(document).ready(function () {
  let selectedFile;

  $('.cardImportRMStock').hide();

  $('#btnImportNewRMStock').click(function (e) {
    e.preventDefault();
    $('.cardCreateRMStock').hide(800);
    $('.cardImportRMStock').toggle(800);
  });

  $('#fileRMStock').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportRMStock').click(function (e) {
    e.preventDefault();

    file = $('#fileRMStock').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    $('.cardBottons').hide();

    let form = document.getElementById('formRMStock');
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
        const expectedHeaders = ['referencia_material', 'material', 'proveedor', 'plazo_minimo', 'plazo_maximo', 'cantidad_minima'];
        const actualHeaders = Object.keys(data[0]);

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileRMStock').val('');
          toastr.error('Archivo no corresponde con el formato. Verifique nuevamente');
          return false;
        }

        let stockToImport = data.map((item) => {
          !item.referencia_material ? item.referencia_material = '' : item.referencia_material;
          !item.material ? item.material = '' : item.material;
          !item.proveedor ? item.proveedor = '' : item.proveedor;
          !item.plazo_minimo ? item.plazo_minimo = 0 : item.plazo_minimo;
          !item.plazo_maximo ? item.plazo_maximo = 0 : item.plazo_maximo;
          !item.cantidad_minima ? item.cantidad_minima = 0 : item.cantidad_minima;

          return {
            refRawMaterial: item.referencia_material,
            nameRawMaterial: item.material,
            client: item.proveedor,
            min: item.plazo_minimo,
            max: item.plazo_maximo,
            quantity: item.cantidad_minima,
          };
        });
        checkStock(stockToImport);
      })
      .catch(() => {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileRMStock').val('');

        toastr.error('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  const checkStock = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/rMStockDataValidation',
      data: { importStock: data },
      success: function (resp) {
        let arr = resp.import;

        if (arr.length > 0 && arr.error == true) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileRMStock').val('');

          $('#formImportRMStock').trigger('reset');
          toastr.error(resp.message);
          return false;
        }

        if (resp.debugg.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#formImportRMStock').val('');

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
                saveStockTable(data);
              } else {
                $('.cardLoading').remove();
                $('.cardBottons').show(400);
                $('#fileRMStock').val('');
              }
            },
          });
        }
      },
    });
  };

  saveStockTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/addRMStock',
      data: { importStock: data },
      success: function (r) {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileRMStock').val('');
        
        messageRMS(r);
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsRMStock').click(async function (e) {
    e.preventDefault();

    $('.cardBottons').hide();

    let form = document.getElementById('formRMStock');
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

    namexlsx = 'Stock_Materiales.xlsx';
    url = '/api/stockMaterials';
    
    let stock = await searchData(url);

    if (stock.length > 0) {
      for (i = 0; i < stock.length; i++) {
        data.push({
          referencia_material: stock[i].reference,
          material: stock[i].material,
          proveedor: stock[i].client,
          plazo_minimo: parseFloat(stock[i].min_term),
          plazo_maximo: parseFloat(stock[i].max_term),
          cantidad_minima: parseFloat(stock[i].min_quantity),
        });
      }

      let ws = XLSX.utils.json_to_sheet(data);
      XLSX.utils.book_append_sheet(wb, ws, 'Stock');
      XLSX.writeFile(wb, namexlsx);
    }

    $('.cardLoading').remove();
    $('.cardBottons').show(400);
    $('#fileRMStock').val('');
  });
});
