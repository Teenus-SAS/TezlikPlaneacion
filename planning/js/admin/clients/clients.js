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
      checkDataClient('/api/addClient', idClient);
    } else {
      checkDataClient('/api/updateClient',idClient);
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
    sessionStorage.setItem('type_client', data.type_client);

    $('#nit').val(data.nit);
    $('#companyName').val(data.client);
    $('#address').val(data.address);
    $('#phone').val(data.phone);
    $('#city').val(data.city); 

    $('#btnCreateClient').html('Actualizar');

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  const checkDataClient = async (url, idClient) => {
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
    
    if (idClient) {
      client.append('idClient', idClient); 

      let type_client = sessionStorage.getItem('type_client');
      client.append('type', type_client); 
    }

    let resp = await sendDataPOST(url, client);
    message(resp);
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
        if (result) {
          $.get(
            `/api/deleteClient/${id_client}`,
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
          if (result) {
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
        if (result) {
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

  // Clonar cliente
  $(document).on('click','.copyClient', function () {
    let data = JSON.parse(sessionStorage.getItem('dataClients'));
    let options = '';

    for (let i = 0; i < data.length; i++) {
      options += `<option value="${data[i].id_client}">${data[i].client}</option>`;     
    }

    bootbox.confirm({
      size: 'small',
      title: 'Clonar Cliente',
      message: `<div class="row">
                  <div class="col-12 floating-label enable-floating-label show-label">
                    <label>Cliente</label>
                    <select class="form-control" id="SCClient">
                      <option disabled selected value="0">Seleccionar</option>
                      ${options}
                    </select>
                  </div>
                  <div class="col-12 floating-label enable-floating-label show-label">
                    <label>Tipo</label>
                    <select class="form-control" id="SType">
                      <option disabled selected value="0">Seleccionar</option>
                      <option value="1">Cliente</option>
                      <option value="2">Proveedor</option>
                    </select>
                  </div>
                </div>`,
      buttons: {
        confirm: {
          label: 'Crear',
          className: 'btn-success',
        },
        cancel: {
          label: 'Cancelar',
          className: 'btn-danger',
        },
      },
      callback: function (result) {
        if (result == true) {
          let SCClient = $('#SCClient').val();
          let SType = $('#SType').val();

          if (!SType || SType == '0' || !SCClient || SCClient == '0') {
            toastr.error('Seleccione datos');
            return false;
          }

          let client = data.find(item => item.id_client == SCClient);

          // if (client.type_client == SType) {
          //   toastr.error('Tipo cliente ya existente en ese cliente');
          //   return false;
          // }

          let dataClient = {};
          dataClient['nit'] = client.nit;
          dataClient['client'] = client.client;
          dataClient['address'] = client.address;
          dataClient['phone'] = client.phone;
          dataClient['city'] = client.city;
          dataClient['type'] = SType;

          $.post(
            '/api/copyClient',
            dataClient,
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
      $('.cardCreateClient').hide(800);
      $('.cardImportClients').hide(800);
      $('#formImportClients').trigger('reset');
      $('#formCreateClient').trigger('reset');
      loadAllDataClients();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };
});
