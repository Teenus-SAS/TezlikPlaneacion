$(document).ready(function () {
  let selectedFile;

  $('.cardImportProducts').hide();

  $('#btnImportNewProducts').click(function (e) {
    e.preventDefault();
    $('.cardCreateProduct').hide(800);
    $('.cardImportProducts').toggle(800);
  });

  $('#fileProducts').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportProducts').click(function (e) {
    e.preventDefault();
    file = $('#fileProducts').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    $('.cardBottons').hide();

    let form = document.getElementById('formProducts');
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

        const expectedHeaders = ['referencia_producto', 'producto', 'existencias'];
        const actualHeaders = Object.keys(data[0]);

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileProducts').val('');
          toastr.error('Archivo no corresponde con el formato. Verifique nuevamente');
          return false;
        }

        let productsToImport = data.map((item) => {
          !item.referencia_producto ? item.referencia_producto = '' : item.referencia_producto;
          !item.producto ? item.producto = '' : item.producto;
          !item.existencias ? item.existencias = 0 : item.existencias;

          return {
            referenceProduct: item.referencia_producto,
            product: item.producto,
            quantity: item.existencias,
          };
        });
        checkProduct(productsToImport);
      })
      .catch(() => {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileProducts').val('');
        toastr.error('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  const checkProduct = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/productsDataValidation',
      data: { importProducts: data },
      success: function (resp) {
        let arr = resp.import;

        if (arr.length > 0 && arr.error == true) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileProducts').val('');
          toastr.error(resp.message);
          $('#formImportProduct').trigger('reset');
          return false;
        }

        if (resp.debugg.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#formImportProduct').val('');

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
            message: `Se encontraron los siguientes registros:<br><br>Datos a insertar: ${arr.insert} <br>Datos a actualizar: ${arr.update}`,
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
                saveProductTable(data);
              } else {
                $('.cardLoading').remove();
                $('.cardBottons').show(400);
                $('#fileProducts').val('');
              }
            },
          });
        }
      },
    });
  };

  /* Guardar Importacion */
  const saveProductTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/addProduct',
      //data: data,
      data: { importProducts: data },
      success: function (r) {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileProducts').val('');
        messageProducts(r);
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsProducts').click(async function (e) {
    e.preventDefault();

    $('.cardBottons').hide();
    
    let form = document.getElementById('formProducts');
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

    namexlsx = 'Productos.xlsx';
    // url = '/api/products';
    
    // let products = await searchData(url);
    let dataProducts = JSON.parse(sessionStorage.getItem('dataProducts'));

    if (dataProducts.length > 0) {
      for (i = 0; i < dataProducts.length; i++) {
        data.push({
          referencia_producto: dataProducts[i].reference,
          producto: dataProducts[i].product,
          existencias: dataProducts[i].quantity
        });
      }

      let ws = XLSX.utils.json_to_sheet(data);
      XLSX.utils.book_append_sheet(wb, ws, 'Productos');
      XLSX.writeFile(wb, namexlsx);
    } else {
      url = 'assets/formatsXlsx/Productos.xlsx';

      link = document.createElement('a');
      link.target = '_blank';

      link.href = url;
      document.body.appendChild(link);
      link.click();

      document.body.removeChild(link);
      delete link;
    }

    $('.cardLoading').remove();
    $('.cardBottons').show(400);
    $('#fileProducts').val('');
  });
 
});
