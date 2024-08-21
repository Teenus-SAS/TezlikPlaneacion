$(document).ready(function () {
  /* Ocultar panel crear producto */

  $('.cardCreateProcess').hide();

  /* Abrir panel crear producto */

  $('#btnNewProcess').click(function (e) {
    e.preventDefault();

    $('.cardImportProcess').hide(800);
    $('.cardCreateProcess').toggle(800);
    $('#btnCreateProcess').html('Crear');

    sessionStorage.removeItem('id_process');

    $('#process').val('');
  });

  /* Crear nuevo proceso */

  $('#btnCreateProcess').click(function (e) {
    e.preventDefault();

    let idProcess = sessionStorage.getItem('id_process');

    if (idProcess == '' || idProcess == null) {
      checkDataProcess('/api/addPlanProcess', idProcess);
    } else {
      checkDataProcess('/api/updatePlanProcess', idProcess);
    }
  });

  /* Actualizar procesos */

  $(document).on('click', '.updateProcess', function (e) {
    $('.cardImportProcess').hide(800);
    $('.cardCreateProcess').show(800);
    $('#btnCreateProcess').html('Actualizar');

    let row = $(this).parent().parent()[0];
    let data = tblProcess.fnGetData(row);

    sessionStorage.setItem('id_process', data.id_process);
    $('#process').val(data.process);

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  /* Revision data procesos */
  const checkDataProcess = async (url, idProcess) => {
    let process = $('#process').val();

    if (process.trim() == '' || !process.trim()) {
      toastr.error('Ingrese todos los campos');
      return false;
    }

    let dataProcess = new FormData(formCreateProcess);

    if (idProcess != '' || idProcess != null)
      dataProcess.append('idProcess', idProcess);

    let resp = await sendDataPOST(url, dataProcess);

    messageProcess(resp);
  };

  /* Eliminar proceso */

  deleteProcessFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblProcess.fnGetData(row);

    let id_process = data.id_process;

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar este proceso? Esta acción no se puede reversar.',
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
            `../../api/deletePlanProcess/${id_process}`,
            function (data, textStatus, jqXHR) {
              messageProcess(data);
            }
          );
        }
      },
    });
  };

  /* Mensaje de exito */

  messageProcess = (data) => {
    if (data.success == true) {
      $('.cardImportProcess').hide(800);
      $('#formImportProcess').trigger('reset');
      $('.cardCreateProcess').hide(800);
      $('#formCreateProcess').trigger('reset');
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblProcess').DataTable().clear();
    $('#tblProcess').DataTable().ajax.reload();
  }
});
