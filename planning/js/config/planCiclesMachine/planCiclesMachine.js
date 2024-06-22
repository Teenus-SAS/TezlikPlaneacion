$(document).ready(function () {
  $('.cardCreatePlanCiclesMachine').hide();

  //Abrir Card crear plan de ciclos maquina
  $('#btnNewPlanCiclesMachine').click(function (e) {
    e.preventDefault();

    $('.cardImportPlanCiclesMachine').hide(800);
    $('.cardCreatePlanCiclesMachine').toggle(800);
    $('#btnCreatePlanCiclesMachine').html('Crear');

    sessionStorage.removeItem('id_cicles_machine');

    $('#formCreatePlanCiclesMachine').trigger('reset');
  });

  //Crear plan ciclos maquina
  $('#btnCreatePlanCiclesMachine').click(function (e) {
    e.preventDefault();
    let id_cicles_machine = sessionStorage.getItem('id_cicles_machine');

    if (id_cicles_machine == '' || id_cicles_machine == null) {
      checkPlanCiclesMachine('/api/addPlanCiclesMachine', id_cicles_machine);
    } else {
      checkPlanCiclesMachine('/api/updatePlanCiclesMachine', id_cicles_machine);
    }
  });

  //Actualizar plan ciclo maquina
  $(document).on('click', '.updatePCMachine', function (e) {
    $('.cardCreatePlanCiclesMachine').show(800);
    $('#btnCreatePlanCiclesMachine').html('Actualizar');

    id_cicles_machine = this.id;
    id_cicles_machine = sessionStorage.setItem(
      'id_cicles_machine',
      id_cicles_machine
    );

    let row = $(this).parent().parent()[0];
    let data = tblPlanCiclesMachine.fnGetData(row);

    $(`#idProcess option[value=${data.id_process}]`).prop('selected', true);
    $(`#idMachine option[value=${data.id_machine}]`).prop('selected', true);
    $('#ciclesHour').val(data.cicles_hour.toLocaleString('es-CO'));

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  checkPlanCiclesMachine = async (url, idCiclesMachine) => {
    let idProcess = parseInt($('#idProcess').val());
    let idMachine = parseInt($('#idMachine').val());
    let idProduct = parseInt($('#selectNameProduct').val());
    let ciclesHour = $('#ciclesHour').val();

    let data = idProcess * idProduct * ciclesHour;

    if (!data || data == '' || data == null || data == 0 || isNaN(idMachine)) {
      toastr.error('Ingrese todos los campos');
      return false;
    }

    let dataPlanCiclesMachine = new FormData(formCreatePlanCiclesMachine);
    dataPlanCiclesMachine.append('idProduct', idProduct);

    if (idCiclesMachine != '' || idCiclesMachine != null)
      dataPlanCiclesMachine.append('idCiclesMachine', idCiclesMachine);

    let resp = await sendDataPOST(url, dataPlanCiclesMachine);

    messageMachine(resp);
  } 

  // Eliminar plan ciclo maquina

  deleteMachine = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblPlanCiclesMachine.fnGetData(row);

    let id_cicles_machine = data.id_cicles_machine;

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar esta maquina? Esta acción no se puede reversar.',
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
          $.get(
            `/api/deletePlanCiclesMachine/${id_cicles_machine}`,
            function (data, textStatus, jqXHR) {
              messageMachine(data);
            }
          );
        }
      },
    });
  };

  /* Mensaje de exito */
  messageMachine = (data) => {
    if (data.success == true) {
      $('.cardCreatePlanCiclesMachine').hide(800);
      $('#formCreatePlanCiclesMachine').trigger('reset');
      $('.cardImportPlanCiclesMachine').hide(800);
      $('#formImportPlanCiclesMachine').trigger('reset');

      if($('#selectNameProduct').val())
        updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */
  function updateTable() {
    $('#tblPlanCiclesMachine').DataTable().clear();
    $('#tblPlanCiclesMachine').DataTable().ajax.reload();
    $('#tblRoutes').DataTable().clear();
    $('#tblRoutes').DataTable().ajax.reload();
  }

  loadDataMachines(1);
});
