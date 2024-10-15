$(document).ready(function () {
  let selectedFile;

  $('.cardImportEmployees').hide();

  $('#btnImportNewEmployee').click(function (e) {
    e.preventDefault();
    $('.cardCreateEmployee').hide(800);
    $('.cardImportEmployees').toggle(800);
  });

  $('#fileEmployees').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportEmployees').click(function (e) {
    e.preventDefault();

    file = $('#fileEmployees').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    $('.cardBottons').hide();

    let form = document.getElementById('formEmployees');
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
        const expectedHeaders = ['nombre', 'apellido', 'area', 'proceso', 'maquina', 'posicion', 'salario_basico', 'horas_trabajo_x_dia', 'dias_trabajo_x_mes', 'tipo_riesgo', 'tipo_nomina', 'disponible'];

        const actualHeaders = Object.keys(data[0]);

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileEmployees').val('');
          toastr.error('Archivo no corresponde con el formato. Verifique nuevamente');
          return false;
        }

        let payrollToImport = data.map((item) => {
          !item.nombre ? item.nombre = '' : item.nombre;
          !item.apellido ? item.apellido = '' : item.apellido;
          !item.area ? item.area = '' : item.area;
          !item.maquina ? item.maquina = '' : item.maquina;
          !item.proceso ? item.proceso = '' : item.proceso;
          !item.posicion ? item.posicion = '' : item.posicion;
          !item.salario_basico ? item.salario_basico = '0' : item.salario_basico;
          !item.transporte ? item.transporte = '0' : item.transporte;
          !item.dotaciones ? item.dotaciones = '0' : item.dotaciones;
          !item.horas_extras ? item.horas_extras = '0' : item.horas_extras;
          !item.otros_ingresos ? item.otros_ingresos = '0' : item.otros_ingresos;
          // !item.prestacional ? item.prestacional = '' : item.prestacional;
          !item.horas_trabajo_x_dia ? item.horas_trabajo_x_dia = '0' : item.horas_trabajo_x_dia;
          !item.dias_trabajo_x_mes ? item.dias_trabajo_x_mes = '0' : item.dias_trabajo_x_mes;
          !item.tipo_riesgo ? item.tipo_riesgo = '' : item.tipo_riesgo;
          !item.tipo_nomina ? item.tipo_nomina = '' : item.tipo_nomina;
          !item.factor ? item.factor = '0' : item.factor;
          !item.disponible ? item.disponible = '' : item.disponible; 

          return {
            firstname: item.nombre,
            lastname: item.apellido,
            area: item.area,
            machine: item.maquina,
            process: item.proceso,
            position: item.posicion,
            basicSalary: item.salario_basico,
            transport: item.transporte,
            endowment: item.dotaciones,
            extraTime: item.horas_extras,
            bonification: item.otros_ingresos,
            // benefit: item.prestacional,
            workingHoursDay: item.horas_trabajo_x_dia,
            workingDaysMonth: item.dias_trabajo_x_mes,
            riskLevel: item.tipo_riesgo,
            typeFactor: item.tipo_nomina,
            factor: item.factor,
            active: item.disponible,
          };
        });
        checkPayroll(payrollToImport);
      })
      .catch(() => {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileEmployees').val('');
        
        toastr.error('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Mensaje de advertencia */
  const checkPayroll = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/payrollDataValidation',
      data: { importPayroll: data },
      success: function (resp) {
        let arr = resp.import;

        if (arr.length > 0 && arr.error == true) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileEmployees').val('');
          $('#formImportEmployees').trigger('reset');
          toastr.error(resp.message);
          return false;
        }

        if (resp.debugg.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#fileEmployees').val('');

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
                saveAreaTable(data);
              } else {
                $('.cardLoading').remove();
                $('.cardBottons').show(400);
                $('#fileEmployees').val('');
              }
            },
          });
        }
      },
    });
  };

  const saveAreaTable = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/addPayroll',
      data: { importPayroll: data },
      success: function (r) {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#fileEmployees').val('');
        messagePayroll(r);
      },
    });
  };

  /* Descargar formato */
  $('#btnDownloadImportsEmployees').click(function (e) {
    e.preventDefault();

    url = 'assets/formatsXlsx/Nomina.xlsx';

    link = document.createElement('a');

    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
