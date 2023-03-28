$(document).ready(function () {
  /* Ocultar panel crear cliente */

  $('.cardCreateClient').hide();

  /* Abrir panel crear cliente */

  $('#btnNewClient').click(function (e) {
    e.preventDefault();

    $('.cardImportClient').hide(800);
    $('.cardCreateClient').toggle(800);
    $('#btnCreateClient').html('Crear');

    sessionStorage.removeItem('id_client');

    $('#formCreateClient').trigger('reset');
  });

  /* Crear nuevo cliente */

  $('#btnCreateClient').click(function (e) {
    e.preventDefault();

    let idClient = sessionStorage.getItem('id_client');

    if (idClient == '' || idClient == null) {
      ean = $('#ean').val();
      nit = $('#nit').val();
      client = $('#client').val();

      if (
        ean == '' ||
        ean == null ||
        nit == '' ||
        nit == null ||
        client == '' ||
        client == null
      ) {
        toastr.error('Ingrese todos los campos');
        return false;
      }

      client = $('#formCreateClient').serialize();

      $.post('../../api/addClient', client, function (data, textStatus, jqXHR) {
        message(data);
      });
    } else {
      updateClient();
    }
  });

  /* Actualizar clientes */

  $(document).on('click', '.updateClient', function (e) {
    $('.cardImportClient').hide(800);
    $('.cardCreateClient').show(800);
    $('#btnCreateClient').html('Actualizar');

    let row = $(this).parent().parent()[0];
    let data = tblClients.fnGetData(row);

    sessionStorage.setItem('id_client', data.id_client);
    $('#ean').val(data.ean.toLocaleString('es-CO'));
    $('#nit').val(data.nit.toLocaleString('es-CO'));
    $('#client').val(data.client);

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateClient = () => {
    let data = $('#formCreateClient').serialize();
    idClient = sessionStorage.getItem('id_client');
    data = data + '&idClient=' + idClient;

    $.post('../../api/updateClient', data, function (data, textStatus, jqXHR) {
      message(data);
    });
  };

  /* Eliminar cliente */
  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblClients.fnGetData(row);

    let id_client = data.id_client;

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar este cliente? Esta acción no se puede reversar.',
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
            `../../api/deleteClient/${id_client}`,
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
      $('.cardCreateClient').hide(800);
      $('#formCreateClient').trigger('reset');
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblClients').DataTable().clear();
    $('#tblClients').DataTable().ajax.reload();
  }
});
