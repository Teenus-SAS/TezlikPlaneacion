$(document).ready(function () {
  // Ocultar Modal Nuevo usuario
  $('#btnCloseUser').click(function (e) {
    e.preventDefault();
    $('#createUserAccess').modal('hide');
  });

  /* Abrir panel Nuevo usuario */

  $('#btnNewUser').click(function (e) {
    e.preventDefault();
    $('#createUserAccess').modal('show');
    $('#btnCreateUserAndAccess').html('Crear Usuario y Accesos');

    sessionStorage.removeItem('id_user');

    $('#nameUser').prop('disabled', false);
    $('#lastnameUser').prop('disabled', false);
    $('#emailUser').prop('disabled', false);

    $('#formCreateUser').trigger('reset');
  });

  /* Agregar nuevo usuario */

  $('#btnCreateUserAndAccess').click(function (e) {
    e.preventDefault();
    let idUser = sessionStorage.getItem('id_user');

    if (idUser == '' || idUser == null) {
      nameUser = $('#nameUser').val();
      lastnameUser = $('#lastnameUser').val();
      emailUser = $('#emailUser').val();

      if (
        nameUser == '' ||
        nameUser == null ||
        lastnameUser == '' ||
        lastnameUser == null ||
        emailUser == '' ||
        emailUser == null
      ) {
        toastr.error('Ingrese nombre, apellido y/o email');
        return false;
      }

      /* Validar que al menos un acceso sea otorgado */
      if ($('input[type=checkbox]:checked').length === 0) {
        toastr.error('Debe seleccionar al menos un acceso');
      }

      /* Obtener los checkbox seleccionados */

      dataUser = {};
      dataUser['nameUser'] = nameUser;
      dataUser['lastnameUser'] = lastnameUser;
      dataUser['emailUser'] = emailUser;

      dataUser = setCheckBoxes(dataUser);

      $.post('/api/addUser', dataUser, function (data, textStatus, jqXHR) {
        message(data);
      });
    } else {
      updateUserAccess();
    }
  });

  /* Actualizar User */

  $(document).on('click', '.updateUser', function (e) {
    $('#createUserAccess').modal('show');
    $('#btnCreateUserAndAccess').html('Actualizar Accesos');

    $('#nameUser').prop('disabled', true);
    $('#lastnameUser').prop('disabled', true);
    $('#emailUser').prop('disabled', true);

    let row = $(this).parent().parent()[0];
    let data = tblUsers.fnGetData(row);

    let idUser = this.id;
    sessionStorage.setItem('id_user', idUser);

    $('#nameUser').val(data.firstname);
    $('#lastnameUser').val(data.lastname);
    $('#emailUser').val(data.email);

    let i = 1;

    let access = {
      createMold: data.create_mold,
      planningCreateProduct: data.create_product,
      planningCreateMaterial: data.create_material,
      planningCreateMachine: data.create_machine,
      planningCreateProcess: data.create_process,
      planningProductsMaterial: data.products_material,
      planningProductsProcess: data.products_process,
      programsMachine: data.programs_machine,
      ciclesMachine: data.cicles_machine,
      invCategory: data.inv_category,
      sale: data.sale,
      plannigUser: data.user,
      client: data.client,
      ordersType: data.orders_type,
      inventory: data.inventory,
      order: data.plan_order,
      program: data.program,
      load: data.plan_load,
      explosionOfMaterial: data.explosion_of_material,
      office: data.office,
    };

    $.each(access, (index, value) => {
      if (value === 1) {
        $(`#checkbox-${i}`).prop('checked', true);
      } else $(`#checkbox-${i}`).prop('checked', false);
      i++;
    });

    $('html, body').animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateUserAccess = () => {
    idUser = sessionStorage.getItem('id_user');

    dataUser = {};
    dataUser['idUser'] = idUser;
    dataUser['nameUser'] = $('#nameUser').val();
    dataUser['lastnameUser'] = $('#lastnameUser').val();
    dataUser['emailUser'] = $('#emailUser').val();

    dataUser = setCheckBoxes(dataUser);

    $.post(
      '/api/updatePlanningUserAccess',
      dataUser,
      function (data, textStatus, jqXHR) {
        message(data);
        updateTable();
      }
    );
  };

  /* Metodo para definir checkboxes */
  setCheckBoxes = (dataUser) => {
    let i = 1;

    let access = {
      createMold: 0,
      planningCreateProduct: 0,
      planningCreateMaterial: 0,
      planningCreateMachine: 0,
      planningCreateProcess: 0,
      planningProductsMaterial: 0,
      planningProductsProcess: 0,
      programsMachine: 0,
      ciclesMachine: 0,
      invCategory: 0,
      sale: 0,
      plannigUser: 0,
      client: 0,
      ordersType: 0,
      inventory: 0,
      order: 0,
      program: 0,
      load: 0,
      explosionOfMaterial: 0,
      office: 0,
    };

    $.each(access, (index, value) => {
      if ($(`#checkbox-${i}`).is(':checked')) dataUser[`${index}`] = 1;
      else dataUser[`${index}`] = 0;
      i++;
    });

    return dataUser;
  };

  /* Eliminar usuario */

  deleteFunction = () => {
    let row = $(this.activeElement).parent().parent()[0];
    let data = tblUsers.fnGetData(row);

    // let idUser = data.id_user;
    let idUser = data.id_user;
    let programsMachine = data.programs_machine;
    dataUser = {};
    dataUser['idUser'] = idUser;
    dataUser['programsMachine'] = programsMachine;

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
        if (result == true) {
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

  /* Mensaje de exito */

  message = (data) => {
    if (data.success == true) {
      $('#createUserAccess').modal('hide');
      $('#formCreateUser').trigger('reset');
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblUsers').DataTable().clear();
    $('#tblUsers').DataTable().ajax.reload();
  }
});
