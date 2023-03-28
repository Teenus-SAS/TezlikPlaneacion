$(document).ready(function () {
  $('.cardCreatePlanCiclesMachine').hide();

  //Abrir Card crear plan de ciclos maquina
  $('#btnNewPlanCiclesMachine').click(function (e) {
    e.preventDefault();

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
      idMachine = parseInt($('#idMachine').val());
      idProduct = parseInt($('#selectNameProduct').val());
      ciclesHour = $('#ciclesHour').val();

      data = idMachine * idProduct * ciclesHour;

      if (!data || data == '' || data == null || data == 0) {
        toastr.error('Ingrese todos los campos');
        return false;
      }

      planCiclesMachine = $('#formCreatePlanCiclesMachine').serialize();
      planCiclesMachine = planCiclesMachine + '&idProduct=' + idProduct;

      $.post(
        '/api/addPlanCiclesMachine',
        planCiclesMachine,
        function (data, textStatus, jqXHR) {
          message(data);
        }
      );
    } else {
      updatePlanCiclesMachine();
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

    $(`#idMachine option:contains(${data.machine})`).prop('selected', true);
    $(`#selectNameProduct option:contains(${data.product})`).prop(
      'selected',
      true
    );
    $('#ciclesHour').val(data.cicles_hour.toLocaleString('es-CO'));

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updatePlanCiclesMachine = () => {
    let data = $('#formCreatePlanCiclesMachine').serialize();
    idProduct = $('#selectNameProduct').val();
    id_cicles_machine = sessionStorage.getItem('id_cicles_machine');
    data = `${data}&idCiclesMachine=${id_cicles_machine}&idProduct=${idProduct}`;

    $.post(
      '/api/updatePlanCiclesMachine',
      data,
      function (data, textStatus, jqXHR) {
        message(data);
      }
    );
  };

  // Eliminar plan ciclo maquina

  deleteFunction = () => {
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
        if (result == true) {
          $.get(
            `/api/deletePlanCiclesMachine/${id_cicles_machine}`,
            function (data, textStatus, jqXHR) {
              message(data);
            }
          );
        }
      },
    });
  };

  /* Mensaje de exito */

  message = (data) => {
    if (data.success == true) {
      $('.cardCreatePlanCiclesMachine').hide(800);
      $('#formCreatePlanCiclesMachine').trigger('reset');
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
  }
});
