$(document).ready(function () {
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

    $("#nameUser").prop("disabled", false);
    $("#lastnameUser").prop("disabled", false);
    $("#emailUser").prop("disabled", false);

    $("#formCreateUser").trigger("reset");
  });

  /* Agregar nuevo usuario */

  $("#btnCreateUserAndAccess").click(function (e) {
    e.preventDefault();
    let id_user = sessionStorage.getItem("id_user");

    if (id_user == "" || id_user == null) {
      nameUser = $("#nameUser").val();
      lastnameUser = $("#lastnameUser").val();
      emailUser = $("#emailUser").val();

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
      }

      /* Obtener los checkbox seleccionados */

      dataUser = {};
      dataUser["nameUser"] = nameUser;
      dataUser["lastnameUser"] = lastnameUser;
      dataUser["emailUser"] = emailUser;

      dataUser = setCheckBoxes(dataUser);

      $.post("/api/addUser", dataUser, function (data, textStatus, jqXHR) {
        message(data, null);
      });
    } else {
      updateUserAccess();
    }
  });

  /* Actualizar User */

  $(document).on("click", ".updateUser", function (e) {
    $("#createUserAccess").modal("show");
    $("#btnCreateUserAndAccess").text("Actualizar Accesos");

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
      // planningCreateMaterial: data.create_material,
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

    $("html, body").animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateUserAccess = () => {
    id_user = sessionStorage.getItem("id_user");

    dataUser = {};
    dataUser["idUser"] = id_user;
    dataUser["nameUser"] = $("#nameUser").val();
    dataUser["lastnameUser"] = $("#lastnameUser").val();
    dataUser["emailUser"] = $("#emailUser").val();

    dataUser = setCheckBoxes(dataUser);

    $.post(
      "/api/updatePlanningUserAccess",
      dataUser,
      function (data, textStatus, jqXHR) {
        message(data, id_user);
        updateTable();
      }
    );
  };

  /* Metodo para definir checkboxes */
  setCheckBoxes = (dataUser) => {
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

  message = async (data, id_user) => {
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
