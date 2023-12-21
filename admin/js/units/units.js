$(document).ready(function () {
  $('.cardCreateUnit').hide();

  /* Abrir card unidades */
  $('#btnNewUnit').click(function (e) {
    e.preventDefault();

    sessionStorage.removeItem('idUnit');
    $('#btnCreateUnit').html('Crear Unidad');
    $('#formCreateUnit').trigger('reset');
    $('.cardCreateUnit').toggle(800);
  });

  /* Crear Unidad */
  $('#btnCreateUnit').click(function (e) {
    e.preventDefault();

    let id_unit = sessionStorage.getItem('idUnit');

    if (!id_unit || id_unit == undefined) {
      let magnitude = $('#magnitude').val();
      let unit = $('#unit').val();
      let abbreviation = $('#abbreviation').val();

      if (magnitude == '' || unit == '' || abbreviation == '') {
        toastr.error('Ingrese todos los campos');
        return false;
      }

      let data = $('#formCreateUnit').serialize();

      $.post('/api/addUnit', data, function (data, textStatus, jqXHR) {
        message(data);
      });
    } else updateUnit();
  });

  /* Actualizar unidad */
  $(document).on('click', '.updateUnit', function () {
    $('#btnCreateUnit').html('Actualizar');

    let idUnit = this.id;
    sessionStorage.setItem('idUnit', idUnit);

    let row = $(this).parent().parent()[0];
    let data = tblUnits.fnGetData(row);

    $(`#magnitudes option[value=${data.id_magnitude}]`).prop('selected', true);
    $('#unit').val(data.unit);
    $('#abbreviation').val(data.abbreviation);

    $('.cardCreateUnit').show(800);

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateUnit = () => {
    let magnitude = $('#magnitude').val();
    let unit = $('#unit').val();
    let abbreviation = $('#abbreviation').val();

    if (magnitude == '' || unit == '' || abbreviation == '') {
      toastr.error('Ingrese todos los campos');
      return false;
    }

    let idUnit = sessionStorage.getItem('idUnit');

    let data = $('#formCreateUnit').serialize();

    data = `${data}&idUnit=${idUnit}`;

    $.post('/api/updateUnit', data, function (data, textStatus, jqXHR) {
      message(data);
    });
  };

  /* Eliminar Unidad */

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblUnits.fnGetData(row);

    let idUnit = data.id_unit;

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar esta unidad? Esta acción no se puede reversar.',
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
            `/api/deleteUnit/${idUnit}`,
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
      $('.cardCreateUnit').hide(800);
      $('#formCreateUnit').trigger('reset');
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  function updateTable() {
    $('#tblUnits').DataTable().clear();
    $('#tblUnits').DataTable().ajax.reload();
  }
});
