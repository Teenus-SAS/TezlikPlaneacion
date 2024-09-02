$(document).ready(function () {
  /* Ocultar panel Nuevo usuario */
  $('.cardCreateUser').hide();

  /* Abrir panel Nuevo usuario */
  $('#btnNewUser').click(function (e) {
    e.preventDefault();
    $('.cardCreateUser').toggle(800);
    $('#btnCreateUser').html('Crear Usuario');

    sessionStorage.removeItem('id_user');
    $('#email').prop('disabled', false);
    $('#company').prop('disabled', false);

    $('#formCreateUser').trigger('reset');
  });

  /* Agregar nuevo usuario */
  $('#btnCreateUser').click(function (e) {
    e.preventDefault();
    let idUser = sessionStorage.getItem('id_user');

    if (idUser == '' || idUser == null) {
      company = $('#company').val();
      firstname = $('#firstname').val();
      lastname = $('#lastname').val();
      email = $('#email').val();

      if (
        firstname == '' ||
        firstname == null ||
        lastname == '' ||
        lastname == null ||
        email == '' ||
        email == null ||
        company == '' ||
        company == null
      ) {
        toastr.error('Ingrese nombre, apellido y/o email');
        return false;
      }

      dataUser = {};
      dataUser['nameUser'] = firstname;
      dataUser['lastnameUser'] = lastname;
      dataUser['emailUser'] = email;
      dataUser['company'] = company;

      dataUser = setDataUserAccess(dataUser);

      $.post('/api/addUser', dataUser, function (data, textStatus, jqXHR) {
        message(data);
      });
    } else {
      updateUser();
    }
  });

  /* Actualizar User */
  $(document).on('click', '.updateUser', function (e) {
    $('.cardCreateUser').show(800);
    $('#btnCreateUser').html('Actualizar');

    const row = $(this).closest("tr")[0];
    let data = tblUsers.fnGetData(row);

    let idUser = this.id;
    sessionStorage.setItem('id_user', idUser);

    $('#firstname').val(data.firstname);
    $('#lastname').val(data.lastname);
    $('#email').val(data.email);
    $('#email').prop('disabled', true);
    $(`#company option[value=${data.id_company}]`).prop('selected', true);
    $('#company').prop('disabled', true);

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateUser = () => {
    idUser = sessionStorage.getItem('id_user');

    dataUser = $('#formCreateUser').serialize();

    dataUser = `${dataUser}&idUser=${idUser}`;

    $.post('/api/updateUser', dataUser, function (data, textStatus, jqXHR) {
      $('#email').prop('disabled', false);
      $('#company').prop('disabled', false);

      message(data);
    });
  };

  /* Eliminar usuario */
  deleteFunction = () => {
    const row = $(this.activeElement).closest("tr")[0];
    let data = tblUsers.fnGetData(row);

    dataUser = {};
    dataUser['idUser'] = data.id_user;
    dataUser['email'] = data.email;

    dataUser = setDataUserAccess(dataUser);

    bootbox.confirm({
      title: 'Eliminar',
      message:
        'Está seguro de eliminar este Usuario? Esta acción no se puede reversar.',
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
          $.post(
            '/api/deleteUser',
            dataUser,
            function (data, textStatus, jqXHR) {
              message(data);
            }
          );
        }
      },
    });
  };

  setDataUserAccess = (dataUser) => {
    dataUser['createMold'] = 1;
    dataUser['planningCreateProduct'] = 1;
    dataUser['planningCreateMaterial'] = 1;
    dataUser['planningCreateMachine'] = 1;
    dataUser['planningCreateProcess'] = 1;
    dataUser['planningProductsMaterial'] = 1;
    dataUser['planningProductsProcess'] = 1;
    dataUser['programsMachine'] = 1;
    dataUser['ciclesMachine'] = 1;
    dataUser['invCategory'] = 1;
    dataUser['sale'] = 1;
    dataUser['plannigUser'] = 1;
    dataUser['client'] = 1;
    dataUser['ordersType'] = 1;
    dataUser['inventory'] = 1;
    dataUser['order'] = 1;
    dataUser['program'] = 1;
    dataUser['load'] = 1;
    dataUser['explosionOfMaterial'] = 1;
    dataUser['office'] = 1;

    return dataUser;
  };

  /* Mensaje de exito */
  message = (data) => {
    if (success) {
      $('.cardCreateUser').hide(800);
      $('#formCreateUser').trigger('reset');
      updateTable();
      toastr.success(message);
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };

  /* Actualizar tabla */
  function updateTable() {
    $('#tblUsers').DataTable().clear();
    $('#tblUsers').DataTable().ajax.reload();
  }
});
