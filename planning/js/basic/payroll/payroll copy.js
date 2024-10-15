$(document).ready(function () {
  let selectedFile;

  $('.cardImportPayroll').hide();

  $('#btnImportNewPayroll').click(function (e) {
    e.preventDefault();
    $('#createPayroll').modal('hide');
    $('.cardImportPayroll').toggle(800);
  });

  $('#filePayroll').change(function (e) {
    e.preventDefault();
    selectedFile = e.target.files[0];
  });

  $('#btnImportPayroll').click(function (e) {
    e.preventDefault();

    let file = $('#filePayroll').val();

    if (!file) {
      toastr.error('Seleccione un archivo');
      return false;
    }

    $('.cardBottons').hide();

    let form = document.getElementById('formPayroll');

    form.insertAdjacentHTML(
      'beforeend',
      `<div class="col-sm-1 cardLoading" style="margin-top: 7px; margin-left: 15px">
        <div class="spinner-border text-secondary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
      </div>`
    );

    importFile(selectedFile)
      .then(async (data) => {
        let arr = data.rowObject;

         if (arr.length == 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#filePayroll').val('');
          toastr.error('Archivo vacio. Verifique nuevamente');
          return false;
        }

        const expectedHeaders = ['nombres_y_apellidos', 'proceso', 'salario_basico', 'transporte', 'dotaciones', 'horas_extras', 'otros_ingresos', 'prestacional', 'horas_trabajo_x_dia', 'dias_trabajo_x_mes', 'tipo_riesgo', 'tipo_nomina', 'factor'];
        const actualHeaders = data.actualHeaders;

        const missingHeaders = expectedHeaders.filter(header => !actualHeaders.includes(header));

        if (missingHeaders.length > 0) {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#filePayroll').val('');

          toastr.error('Archivo no corresponde a el formato. Verifique nuevamente');
          return false;
        }
        let resp = await validateDataPy(arr);
 
        if (resp.importStatus == true)
          checkPayroll(resp.payrollToImport, resp.insert, resp.update);
      })
      .catch(() => {
        console.log('Ocurrio un error. Intente Nuevamente');
      });
  });

  /* Validar Data */
  const validateDataPy = async (data) => {
    let payrollToImport = [];
    let importStatus = true;
    let insert = 0;
    let update = 0;

    for (let i = 0; i < data.length; i++) {
      let arr = data[i];

      let basicSalary = '0';
      let transport = '0';
      let endowment = '0';
      let extraTime = '0';
      let bonification = '0';

      if (arr.salario_basico > 0) {
        basicSalary = arr.salario_basico.toString();
      }
      if (arr.transporte > 0) {
        transport = arr.transporte.toString();
      }
      if (arr.dotaciones > 0) {
        endowment = arr.dotaciones.toString();
      }
      if (arr.horas_extras > 0) {
        extraTime = arr.horas_extras.toString();
      }
      if (arr.otros_ingresos > 0) {
        bonification = arr.otros_ingresos.toString();
      }

      // Validación de campos vacíos o nulos
      if (
        !arr.nombres_y_apellidos || !arr.proceso || !arr.prestacional || !arr.horas_trabajo_x_dia ||
        !arr.dias_trabajo_x_mes || !arr.tipo_riesgo || !arr.tipo_nomina || !arr.factor ||
        basicSalary.trim() == '' || transport.trim() == '' || endowment.trim() == ''||
        extraTime.trim() == '' || bonification.trim() == '' 
      ) {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#filePayroll').val('');
        importStatus = false;

        toastr.error(`Columna vacía en la fila: ${i + 2}`);
        break;
      }

      // Validación de campos que no están vacíos o nulos pero son solo espacios
      if (
        !arr.nombres_y_apellidos.toString().trim() || !arr.proceso.toString().trim() || !arr.prestacional.toString().trim() || !arr.horas_trabajo_x_dia.toString().trim() ||
        !arr.dias_trabajo_x_mes.toString().trim() || !arr.tipo_riesgo.toString().trim() || !arr.tipo_nomina.toString().trim() || !arr.factor.toString().trim()
      ) {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#filePayroll').val('');
        importStatus = false;

        toastr.error(`Columna vacía en la fila: ${i + 2}`);
        break;
      }

      let valWorkingDaysMonth = parseFloat(arr.dias_trabajo_x_mes.toString().replace(',', '.'));
      let valWorkingHoursDay = parseFloat(arr.horas_trabajo_x_dia.toString().replace(',', '.'));
      if (valWorkingDaysMonth > 31 || valWorkingHoursDay > 24) {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#filePayroll').val('');
        importStatus = false;

        toastr.error(`El campo dias trabajo x mes debe ser menor a 31 y horas trabajo x dia menor a 24. Fila: ${i + 2}`);
        break;
      }

      // Validar Proceso
      let dataProcess = JSON.parse(sessionStorage.getItem('dataProcess'));
      let process = dataProcess.find(item => item.process == arr.proceso.toString().toUpperCase().trim());

      if (!process) {
        $('.cardLoading').remove();
        $('.cardBottons').show(400);
        $('#filePayroll').val('');
        importStatus = false;

        toastr.error(`Proceso no existe en la base de datos. Fila: ${i + 2}`);
        break;
      }

      payrollToImport.push({ idProcess: process.id_process }); 

      // Obtener Data Prestaciones
      // let dataBenefits = JSON.parse(sessionStorage.getItem('dataBenefits'));
      
      // Obtener data segun el nivel de riesgo
      let dataRisks = JSON.parse(sessionStorage.getItem('dataRisks'));
      let risk = dataRisks.find(item => item.risk_level == arr.tipo_riesgo.toString().toUpperCase().trim());

      payrollToImport[i].valueRisk = risk.percentage;
      payrollToImport[i].risk = risk.id_risk;

      // arr.prestacional == 'SI' ? payrollToImport[i].salary =

      // Validar Nomina
      let dataPayroll = JSON.parse(sessionStorage.getItem('dataPayroll'));
      let payroll = dataPayroll.find(item => item.employee == arr.nombres_y_apellidos.toString().toUpperCase().trim() &&
        item.id_process == process.id_process);
      
      !payroll ? insert += 1 : update += 1;

      payrollToImport[i].employee = arr.nombres_y_apellidos;
      payrollToImport[i].process = arr.proceso;
      payrollToImport[i].basicSalary = arr.salario_basico;
      payrollToImport[i].transport = arr.transporte;
      payrollToImport[i].endowment = arr.dotaciones;
      payrollToImport[i].extraTime = arr.horas_extras;
      payrollToImport[i].bonification = arr.otros_ingresos;
      payrollToImport[i].benefit = arr.prestacional;
      payrollToImport[i].workingHoursDay = arr.horas_trabajo_x_dia;
      payrollToImport[i].workingDaysMonth = arr.dias_trabajo_x_mes;
      payrollToImport[i].riskLevel = arr.tipo_riesgo;
      payrollToImport[i].typeFactor = arr.tipo_nomina;
      payrollToImport[i].factor = arr.factor;
    }

    return { importStatus, payrollToImport, insert, update};
  };

  /* Mensaje de advertencia */
  const checkPayroll = (data, insert, update) => { 
    bootbox.confirm({
      title: '¿Desea continuar con la importación?',
      message: `Se han encontrado los siguientes registros:<br><br>Datos a insertar: ${insert} <br>Datos a actualizar: ${update}`,
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
          savePayroll(data);
        } else {
          $('.cardLoading').remove();
          $('.cardBottons').show(400);
          $('#filePayroll').val('');
        }
      },
    }); 
  };

  const savePayroll = (data) => {
    $.ajax({
      type: 'POST',
      url: '/api/addPayroll',
      data: { importPayroll: data },
      success: function (r) {
        message(r);
      },
    });
  };

  $('#btnDownloadImportsPayroll').click(function (e) {
    e.preventDefault();

    let url = 'assets/formatsXlsx/Carga_Nomina.xlsx';

    let link = document.createElement('a');
    link.target = '_blank';

    link.href = url;
    document.body.appendChild(link);
    link.click();

    document.body.removeChild(link);
    delete link;
  });
});
