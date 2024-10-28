$(document).ready(function () {
  loadDataMachines(3);

  // Ocultar Modal Nuevo usuario
  $("#btnCloseUser").click(function (e) {
    e.preventDefault();
    $("#createUserAccess").modal("hide");
  });

  /* Abrir panel Nuevo usuario */
  $("#btnNewUser").click(function (e) {
    e.preventDefault();
    $("#createUserAccess").modal("show");
    $("#btnCreateUserAndAccess").text("Crear Usuario y Accesos");

    sessionStorage.removeItem("id_user");

    $('.cardTypeMachineOP').hide();
    $("#nameUser").prop("disabled", false);
    $("#lastnameUser").prop("disabled", false);
    $("#emailUser").prop("disabled", false);

    $("#formCreateUser").trigger("reset");
  });

  $(document).on('click', '.typeCheckbox', function () {
    let option = this.id;

    switch (option) {
      case 'checkbox-17':
        $('.cardTypeMachineOP').toggle(800);
        break;
    }
  });

  /* Agregar nuevo usuario */
  $("#btnCreateUserAndAccess").click(function (e) {
    e.preventDefault();
    let id_user = sessionStorage.getItem("id_user");

    if (id_user == "" || id_user == null) {
      checkDataUserAcces('/api/addUser', null);
    } else {
      checkDataUserAcces('/api/updatePlanningUserAccess', id_user);
    }
  });

  /* Actualizar User */
  $(document).on("click", ".updateUser", function (e) {
    $("#createUserAccess").modal("show");
    $("#btnCreateUserAndAccess").text("Actualizar Accesos");
    $('.cardTypeMachineOP').hide();
    $("#nameUser").prop("disabled", true);
    $("#lastnameUser").prop("disabled", true);
    $("#emailUser").prop("disabled", true);

    //obtener data
    const row = $(this).closest("tr")[0];
    let data = tblUsers.fnGetData(row);

    let id_user = this.id;
    sessionStorage.setItem("id_user", id_user);

    $("#nameUser").val(data.firstname);
    $("#lastnameUser").val(data.lastname);
    $("#emailUser").val(data.email);

    let i = 1;

    let access = {
      planningCreateProduct: data.create_product,
      planningCreateMachine: data.create_machine,
      payroll: data.payroll,
      planningProductsMaterial: data.products_material,
      programsMachine: data.programs_machine,
      stock: data.stock,
      calendar: data.calendar,
      client: data.client,
      seller: data.seller,
      sale: data.sale,
      plannigUser: data.user,
      inventory: data.inventory,
      requisition: data.requisition,
      order: data.plan_order,
      program: data.program,
      explosionOfMaterial: data.explosion_of_material,
      productionOrder: data.production_order,
      opToStore: data.op_to_store,
      store: data.store,
      office: data.office,
    };

    $.each(access, (index, value) => {
      if (value == 1) {
        $(`#checkbox-${i}`).prop("checked", true);
      } else $(`#checkbox-${i}`).prop("checked", false);
      i++;
    });

    if (data.production_order == 1) {
      $('.cardTypeMachineOP').show();
      $(`#typeMachineOP option[value=${data.type_machine_op}]`).prop("selected", true);
    }

    $("html, body").animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  const checkDataUserAcces = async (url, idUser) => {
    let nameUser = $("#nameUser").val();
    let lastnameUser = $("#lastnameUser").val();
    let emailUser = $("#emailUser").val();

    if (
      nameUser == "" ||
      nameUser == null ||
      lastnameUser == "" ||
      lastnameUser == null ||
      emailUser == "" ||
      emailUser == null
    ) {
      toastr.error("Ingrese nombre, apellido y/o email");
      return false;
    }

    /* Validar que al menos un acceso sea otorgado */
    if ($("input[type=checkbox]:checked").length === 0) {
      toastr.error("Debe seleccionar al menos un acceso");
      return false;
    }
    
    let dataUser = {};

    if ($(`#checkbox-17`).is(':checked')) {
      let machine = parseFloat($('#typeMachineOP').val());

      if (isNaN(machine)) {
        toastr.error("Debe seleccionar una maquina");
        return false;
      }

      dataUser["typeMachineOP"] = machine;
    } else {
      dataUser["typeMachineOP"] = 0;      
    }

    dataUser["nameUser"] = nameUser;
    dataUser["lastnameUser"] = lastnameUser;
    dataUser["emailUser"] = emailUser;
    
    if (idUser)
      dataUser['idUser'] = idUser;
    
    /* Obtener los checkbox seleccionados */
    dataUser = setCheckBoxes(dataUser);

    await $.ajax({
      type: "POST",
      url: url,
      data: dataUser,
      success: function (resp) {
        message(resp)
      }
    });
  };

  /* Metodo para definir checkboxes */
  const setCheckBoxes = (dataUser) => {
    let i = 1;

    let access = {
      planningCreateProduct: 0,
      planningCreateMachine: 0,
      payroll: 0,
      planningProductsMaterial: 0,
      programsMachine: 0,
      stock: 0,
      calendar: 0,
      client: 0,
      seller: 0,
      sale: 0,
      plannigUser: 0,
      inventory: 0,
      requisition: 0,
      order: 0,
      program: 0,
      explosionOfMaterial: 0,
      productionOrder: 0,
      opToStore: 0,
      store: 0,
      office: 0,
    };

    $.each(access, (index, value) => {
      if ($(`#checkbox-${i}`).is(":checked")) dataUser[`${index}`] = 1;
      else dataUser[`${index}`] = 0;
      i++;
    });

    return dataUser;
  };

  /* Eliminar usuario */

  deleteFunction = () => {
    const row = $(this.activeElement).closest("tr")[0];
    let data = tblUsers.fnGetData(row);

    let id_user = data.id_user;
    let programsMachine = data.programs_machine;
    dataUser = {};
    dataUser["idUser"] = id_user;
    dataUser["programsMachine"] = programsMachine;

    bootbox.confirm({
      title: "Eliminar",
      message:
        "Está seguro de eliminar este Usuario? Esta acción no se puede reversar.",
      buttons: {
        confirm: {
          label: "Si",
          className: "btn-success",
        },
        cancel: {
          label: "No",
          className: "btn-danger",
        },
      },
      callback: function (result) {
        if (result) {
          $.post(
            "/api/deleteUser",
            dataUser,
            function (data, textStatus, jqXHR) {
              message(data, id_user);
            }
          );
        }
      },
    });
  };

  /* Mensaje de exito */

  const message = async (data, id_user) => {
    const { success, error, info, message } = data;
    if (success) {
      $("#createUserAccess").modal("hide");
      $("#formCreateUser").trigger("reset");
      updateTable();
      if (id_user == idUser) await loadUserAccess();
      toastr.success(message);
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $("#tblUsers").DataTable().clear();
    $("#tblUsers").DataTable().ajax.reload();
  }
});
