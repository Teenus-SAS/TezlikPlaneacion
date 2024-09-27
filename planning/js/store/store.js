$(document).ready(function () {
  // Recibir MP
  $(document).on("click", ".changeDateMP", function (e) {
    e.preventDefault();

    let date = new Date().toISOString().split("T")[0];
    const row = $(this).closest("tr")[0];
    let data = tblStore.fnGetData(row);

    bootbox.confirm({
      title: "Ingrese Fecha De Ingreso!",
      message: `<div class="col-sm-12 floating-label enable-floating-label">
                    <input class="form-control" type="date" name="date" id="dateOC" max="${date}"></input>
                    <label for="date">Fecha</span></label>
                </div>`,
      buttons: {
        confirm: {
          label: "Agregar",
          className: "btn-success",
        },
        cancel: {
          label: "Cancelar",
          className: "btn-danger",
        },
      },
      callback: function (result) {
        if (result) {
          let date = $("#dateOC").val();

          if (!date) {
            toastr.error("Ingrese los campos");
            return false;
          }

          let form = new FormData();
          form.append("idRequisition", data.id_requisition);
          form.append("idMaterial", data.id_material);
          form.append("date", date);

          $.ajax({
            type: "POST",
            url: "/api/saveAdmissionDate",
            data: form,
            contentType: false,
            cache: false,
            processData: false,
            success: function (resp) {
              message(resp, 1);
            },
          });
        }
      },
    });
  });

  $(document).on('click', '.seeReceiveOC', async function (e) {
    e.preventDefault();

    const row = $(this).closest("tr")[0];
    let data = tblStore.fnGetData(row);

    let users = await searchData(`/api/usersRequisitions/${data.id_requisition}`);
    let rows = '';

    for (let i = 0; i < users.length; i++) {
      rows +=
        `<tr>
          <td>${i + 1}</td>
          <td>${users[i].firstname}</td>
          <td>${users[i].lastname}</td>
          <td>${users[i].email}</td>
        </tr>`;
    }

    // Mostramos el mensaje con Bootbox
    bootbox.alert({
      title: 'Usuarios',
      message: `
            <div class="container">
              <div class="col-12">
                <div class="table-responsive">
                  <table class="fixed-table-loading table table-hover">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Email</th>
                      </tr>
                    </thead>
                    <tbody>
                      ${rows}
                    </tbody>
                  </table>
                </div>
              </div> 
            </div>`,
      size: 'large',
      backdrop: true
    });
    return false;
  });

  // Entregar MP

  const itemsToRemove = ["idProgramming", "idMaterial", "stored", "pending", "delivered"];
  itemsToRemove.forEach((item) => sessionStorage.removeItem(item));

  /*   sessionStorage.removeItem("idMaterial");
  sessionStorage.removeItem("stored");
  sessionStorage.removeItem("pending");
  sessionStorage.removeItem("delivered"); */

  $(".cardAddDelivery").hide();

  $("#btnDelivery").click(function (e) {
    e.preventDefault();

    $("#formAddDelivery").trigger("reset");
    $(".cardAddDelivery").toggle(800);
  });

  $(document).on("click", ".deliver", function () {
    const row = $(this).closest("tr")[0];
    let data = tblStore.fnGetData(row);
    let id_material = data.id_material;
    let id_programming = data.id_programming;

    let quantity = parseFloat(formatNumber(data.quantity));

    let reserved = parseFloat(formatNumber(data.reserved1)); 

    bootbox.confirm({
      title: "Entrega Material",
      message: `<div class="col-sm-6 floating-label enable-floating-label show-label">
                    <label for="">Cantidad a Entregar</label>
                    <input type="number" class="form-control text-center" id="quantity" name="quantity">
                </div>`,
      buttons: {
        confirm: {
          label: "Guardar",
          className: "btn-success",
        },
        cancel: {
          label: "Cancelar",
          className: "btn-danger",
        },
      },
      callback: function (result) {
        if (result) {
          let store = parseFloat($("#quantity").val());

          if (!store || store <= 0) {
            toastr.error("Ingrese todos los campos");
            return false;
          }

          // if (store > delivery_pending) {
          //     toastr.error('Cantidad a entregar mayor');
          //     return false;
          // }

          store <= reserved ? (pending = reserved - store) : (pending = 0);

          sessionStorage.setItem("idMaterial", id_material);
          sessionStorage.setItem("idProgramming", id_programming);
          sessionStorage.setItem("stored", (quantity - store) < 0 ? 0 : (quantity - store));
          sessionStorage.setItem("pending", pending);
          sessionStorage.setItem("delivered", store);
          saveDeliverMaterial();
        }
      },
    });
  });

  const saveDeliverMaterial = () => {
    let dataStore = {};
    dataStore["idMaterial"] = sessionStorage.getItem("idMaterial");
    dataStore["idProgramming"] = sessionStorage.getItem("idProgramming");
    dataStore["stored"] = sessionStorage.getItem("stored");
    dataStore["pending"] = sessionStorage.getItem("pending");
    dataStore["delivered"] = sessionStorage.getItem("delivered");

    $.ajax({
      type: "POST",
      url: "/api/deliverStore",
      data: dataStore,
      success: function (resp) {
        message(resp, 2);
      },
    });
  };

  $(document).on('click', '.seeDeliverOC', async function (e) {
    e.preventDefault();

    const row = $(this).closest("tr")[0];
    let data = tblStore.fnGetData(row);

    let users = await searchData(`/api/usersStore/${data.id_programming}/${data.id_material}`);
    let rows = '';

    for (let i = 0; i < users.length; i++) {
      rows +=
        `<tr>
          <td>${i + 1}</td>
          <td>${users[i].firstname}</td>
          <td>${users[i].lastname}</td>
          <td>${users[i].email}</td>
          <td>
          ${parseFloat(users[i].delivery_store).toLocaleString("es-CO", {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2,
          })}
          </td>
        </tr>`;
    }

    // Mostramos el mensaje con Bootbox
    bootbox.alert({
      title: 'Usuarios',
      message: `
            <div class="container">
              <div class="col-12">
                <div class="table-responsive">
                  <table class="fixed-table-loading table table-hover">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Email</th>
                        <th>Cantidad Entregada</th>
                      </tr>
                    </thead>
                    <tbody>
                      ${rows}
                    </tbody>
                  </table>
                </div>
              </div> 
            </div>`,
      size: 'large',
      backdrop: true
    });
    return false;
  });

  const message = (data, op) => {
    const { success, error, info, message } = data;
    if (success) {
      /* sessionStorage.removeItem("idMaterial");
      sessionStorage.removeItem("stored");
      sessionStorage.removeItem("pending");
      sessionStorage.removeItem("delivered"); */

      const itemsToRemove = ["idMaterial", "stored", "pending", "delivered"];
      itemsToRemove.forEach((item) => sessionStorage.removeItem(item));

      $("#formDeliverMaterial").trigger("reset");
      $("#deliverMaterial").modal("hide");
      toastr.success(message);

      loadAllData(op);
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };

  function formatNumber(num) {
    // Si el número es un entero, simplemente lo devuelve
    if (Number.isInteger(num)) {
      return num.toString();
    }
    // Si tiene decimales, devuelve el número con dos decimales
    return num.toFixed(2);
  }
});
