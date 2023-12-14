$(document).ready(function () {
  /* Ocultar panel crear cliente */
$('#btnCloseClient').click(function (e) {
    e.preventDefault();
    $('#createClients').modal('hide');
  });

  /* Abrir panel crear cliente */

  $('#btnNewClient').click(function (e) {
    e.preventDefault();

    $('.cardImportClient').hide(800);
    $('#createClients').modal('show');

    $('#btnCreateClient').html('Crear');

    sessionStorage.removeItem('id_client');

    $('#formCreateClient').trigger('reset');
  });

  /* Crear nuevo cliente */

  $('#btnCreateClient').click(function (e) {
    e.preventDefault();

    let idClient = sessionStorage.getItem('id_client');

    if (idClient == '' || idClient == null) {
      let nit = $('#nit').val();
      let companyName = $('#companyName').val();
      let address = $('#address').val();
      let phone = $('#phone').val();
      let city = $('#city').val();

      if (
        nit == '' ||
        companyName == '' ||
        address == '' ||
        phone == '' ||
        city == ''
      ) {
        toastr.error('Ingrese todos los campos');
        return false;
      }

      let imgClient = $('#formFile')[0].files[0];

      let client = new FormData(formCreateClient);
      client.append('img', imgClient);

      $.ajax({
        type: 'POST',
        url: '/api/addClient',
        data: client,
        contentType: false,
        cache: false,
        processData: false,

        success: function (resp) {
          $('#createClients').modal('hide');
          $('#formFile').val('');
          message(resp);
          updateTable();
        },
      });
      
    } else {
      updateClient();
    }
  });

  /* Actualizar clientes */

  $(document).on('click', '.updateClient', function (e) {
    $('.cardImportClient').hide(800); 
    $('#btnCreateClient').html('Actualizar');

    let row = $(this).parent().parent()[0];
    let data = tblClients.fnGetData(row);

    sessionStorage.setItem('id_client', data.id_client);

    $('#nit').val(data.nit);
    $('#companyName').val(data.client);
    $('#address').val(data.address);
    $('#phone').val(data.phone);
    $('#city').val(data.city);
    if (data.img) avatar.src = data.img;

    $('#createClients').modal('show');
    $('#btnCreateClient').html('Actualizar');

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateClient = () => {
    idClient = sessionStorage.getItem('id_client');
    let imgCompany = $('#formFile')[0].files[0];

    let company = new FormData(formCreateClient);
    company.append('idClient', idClient);
    company.append('img', imgCompany);

    $.ajax({
      type: 'POST',
      url: '/api/updateClient',
      data: company,
      contentType: false,
      cache: false,
      processData: false,

      success: function (resp) {
        $('#createClients').modal('hide');
        $('#formFile').val('');
        message(resp);
        updateTable();
      },
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

  /* Camniar usuario interno */
  $(document).on('click', '.checkClient', function () {
    let id = this.id;

    let id_client = id.slice(6, id.length);
    if ($(`#${id}`).is(':checked')) {
      bootbox.confirm({
        title: 'Cliente Interno',
        message:
          'Está seguro de cambiar este cliente interno? Esta acción no se puede reversar.',
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
              `../../api/changeStatus/${id_client}`,
              function (data, textStatus, jqXHR) {
                message(data);
              }
            );
          } else {
            $(`#${id}`).prop('checked', false);
          }
        },
      });
    } else {
      toastr.error('Debe haber por lo menos un cliente interno');
      return false;
    }
  });

  $(document).on('click', '.changeType', function () {
    let row = $(this).parent().parent()[0];
    let data = tblClients.fnGetData(row);

    let id_client = data.id_client;
    let type_client = data.type_client;

    type_client == 1 ? msg = 'Proveedor' : msg = 'Cliente';

    bootbox.confirm({
      title: 'Tipo de Cliente',
      message: `Está seguro de cambiar a ${msg}?`,
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
          type_client == 1 ? op = 2 : op = 1;

          $.get(
            `../../api/changeTypeClient/${id_client}/${op}`,
            function (data, textStatus, jqXHR) {
              message(data);
            }
          );
        } 
      },
    });
    
  });

  /* Mensaje de exito */

  message = (data) => {
    if (data.success == true) { 
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
