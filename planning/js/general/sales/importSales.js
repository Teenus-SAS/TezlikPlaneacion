$(document).ready(function () { 

  $('.cardImportSales').hide();

  $('#btnImportNewSales').click(function (e) {
    e.preventDefault();
    $('.cardImportSales').toggle(800);
  });

  // $('#fileSales').change(function (e) {
  //   e.preventDefault();
  //   selectedFile = e.target.files[0];
  // });

  $('#btnImportSales').click(function (e) {
    e.preventDefault();

    const fileInput = document.getElementById('fileSales');
    const selectedFile = fileInput.files[0];

    if (!selectedFile) {
      toastr.error('Seleccione un archivo');
      return false;
    }
    $('.cardBottons').hide();

    let form = document.getElementById('formSales');

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
        const expectedHeaders = ['referencia','producto','enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
        const actualHeaders = Object.keys(data[0]);

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileSales').val('');

          toastr.error('Archivo no corresponde a el formato. Verifique nuevamente');
          return false;
        }


        let SalesToImport = data.map((item) => {
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
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileSales').val('');

        toastr.error('Ocurrio un error. Intente Nuevamente');
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
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileSales').val('');

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
            } else {
              $('.cardLoading').remove();
              $('.cardBottons').show(400);
              $('#fileSales').val('');
            }
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
        $('.cardLoading').remove();
        $('.cardBottons').show(400); 
        $('#fileSales').val('');
        
        message(r);
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsSales').click(async function (e) {
    e.preventDefault();
    $('.cardBottons').hide();

    let form = document.getElementById('formSales');

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

    namexlsx = 'Unidades_Ventas.xlsx';
    url = '/api/productUnitSales';
    
    let sales = await searchData(url);

    if (sales.length > 0) {
      for (i = 0; i < sales.length; i++) {
        data.push({
          referencia: sales[i].reference,
          producto: sales[i].product,
          enero: sales[i].jan,
          febrero: sales[i].feb,
          marzo: sales[i].mar,
          abril: sales[i].apr,
          mayo: sales[i].may,
          junio: sales[i].jun,
          julio: sales[i].jul,
          agosto: sales[i].aug,
          septiembre: sales[i].sept,
          octubre: sales[i].oct,
          noviembre: sales[i].nov,
          diciembre: sales[i].dece,
        });
      }

      let ws = XLSX.utils.json_to_sheet(data);
      XLSX.utils.book_append_sheet(wb, ws, 'Unidades_Ventas');
      XLSX.writeFile(wb, namexlsx);
    }

    $('.cardLoading').remove();
    $('.cardBottons').show(400);
    $('#fileSales').val('');
  });
});
