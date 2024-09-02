$(document).ready(function () {
  // Recibir MP
  $(document).on("click", ".changeDate", function (e) {
    e.preventDefault();

    let date = new Date().toISOString().split("T")[0];
    const row = $(this).closest("tr")[0];
    let data = tblStore.fnGetData(row);

    bootbox.confirm({
      title: "Ingrese Fecha De Ingreso!",
      message: `<div class="col-sm-12 floating-label enable-floating-label">
                        <input class="form-control" type="date" name="date" id="date" max="${date}"></input>
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
          let date = $("#date").val();

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

  // Entregar MP
  sessionStorage.removeItem("idMaterial");
  sessionStorage.removeItem("stored");
  sessionStorage.removeItem("pending");
  sessionStorage.removeItem("delivered");

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
    let quantity = data.quantity;
    let reserved = data.reserved1;

    data.delivery_pending == 0 && data.delivery_date == null
      ? (delivery_pending = reserved)
      : (delivery_pending = data.delivery_pending);

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
          sessionStorage.setItem("stored", quantity - store);
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
  // $('#btnSaveDeliver').click(function (e) {
  //     e.preventDefault();

  //     let email = $('#email').val();
  //     let password = $('#password').val();

  //     if (!email || !password) {
  //         toastr.error('Ingrese los datos');
  //         return false;
  //     }

  //     let dataStore = {};
  //     dataStore['idMaterial'] = sessionStorage.getItem('idMaterial');
  //     dataStore['stored'] = sessionStorage.getItem('stored');
  //     dataStore['pending'] = sessionStorage.getItem('pending');
  //     dataStore['delivered'] = sessionStorage.getItem('delivered');
  //     // dataStore['email'] = email;
  //     // dataStore['password'] = password;

  //     $.ajax({
  //         type: "POST",
  //         url: '/api/deliverStore',
  //         data: dataStore,
  //         success: function (resp) {
  //             message(resp, 2);
  //         }
  //     });
  // });

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
});
