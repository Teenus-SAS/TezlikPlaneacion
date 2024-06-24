$(document).ready(function () {
  $('.cardCreateMagnitude').hide();

  /* Abrir card Magnitudes */
  $('#btnNewMagnitude').click(function (e) {
    e.preventDefault();

    sessionStorage.removeItem('idMagnitude');

    $('#btnCreateMagnitude').html('Crear Magnitud');
    $('#formCreateMagnitude').trigger('reset');
    $('.cardCreateMagnitude').toggle(800);
  });

  /* Crear Magnitud */
  $('#btnCreateMagnitude').click(function (e) {
    e.preventDefault();

    let id_magnitude = sessionStorage.getItem('idMagnitude');

    if (!id_magnitude || id_magnitude == undefined) {
      let magnitude = $('#magnitude').val();

      if (magnitude == '') {
        toastr.error('Ingrese el campo');
        return false;
      }

      let data = $('#formCreateMagnitude').serialize();

      $.post('/api/addMagnitudes', data, function (data, textStatus, jqXHR) {
        message(data);
      });
    } else updateMagnitude();
  });

  /* Actualizar Magnitud */
  $(document).on('click', '.updateMagnitude', function () {
    $('#btnCreateMagnitude').html('Actualizar');

    // Obtener el ID del elemento
    let id = $(this).attr('id');
    // Obtener la parte después del guion '-'
    let idMagnitude = id.split('-')[1]; 

    sessionStorage.setItem('idMagnitude', idMagnitude);

    let row = $(this).parent().parent()[0];
    let data = tblMagnitudes.fnGetData(row);

    $('#magnitude').val(data.magnitude);

    $('.cardCreateMagnitude').show(800);

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateMagnitude = () => {
    let magnitude = $('#magnitude').val();

    if (magnitude == '') {
      toastr.error('Ingrese el campo');
      return false;
    }

    let idMagnitude = sessionStorage.getItem('idMagnitude');

    let data = $('#formCreateMagnitude').serialize();

    data = `${data}&idMagnitude=${idMagnitude}`;

    $.post('/api/updateMagnitude', data, function (data, textStatus, jqXHR) {
      message(data);
    });
  };

  /* Eliminar Magnitud */

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblMagnitudes.fnGetData(row);

    let idMagnitude = data.id_magnitude;

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar esta magnitud? Esta acción no se puede reversar.',
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
            `/api/deleteMagnitude/${idMagnitude}`,
            data,
            function (data, textStatus, jqXHR) {
              message(data);
            }
          );
        }
      },
    });
  };

  message = (data) => {
    if (data.success == true) {
      $('.cardCreateMagnitude').hide(800);
      $('#formCreateMagnitude').trigger('reset');
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  function updateTable() {
    $('#tblMagnitudes').DataTable().clear();
    $('#tblMagnitudes').DataTable().ajax.reload();
  }
});
