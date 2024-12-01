$(document).ready(function () {
  let storedDLVS = [];

  // Recibir MP
  $(document).on("click", ".changeDateMP", function (e) {
    e.preventDefault();

    let date = new Date().toISOString().split("T")[0];
    const row = $(this).closest("tr")[0];
    let data = tblReceiveOC.fnGetData(row);

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
          form.append("idRequisition", data.id_requisition_material);
          form.append("referenceProduct", data.reference);
          form.append("product", data.material);
          form.append("idMaterial", data.id_material);
          form.append("order", data.num_order);
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
    let data = tblReceiveOC.fnGetData(row);

    let users = await searchData(`/api/usersRequisitions/${data.id_requisition_material}`);
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

  $(".cardAddDelivery").hide();

  $("#btnDelivery").click(function (e) {
    e.preventDefault();

    $("#formAddDelivery").trigger("reset");
    $(".cardAddDelivery").toggle(800);
  });

  $(document).on("click", ".deliver", function () {
    const row = $(this).closest("tr")[0];
    let data = tblDeliverOP.fnGetData(row);
    let id_material = data.id_material;
    let reference = data.reference;
    let material = data.material;
    let id_programming = data.id_programming;
    let delivery_store = parseFloat(data.delivery_store);

    let quantity = parseFloat(formatNumber(data.quantity));

    let reserved = parseFloat(formatNumber(data.reserved1)); 

    if (data.abbreviation == 'UND') {
      quantity = Math.floor(quantity);
      reserved = Math.floor(reserved);
    }

    bootbox.confirm({
      title: `<div style="border-bottom: 1px solid #e3e3e3;">Entregar Materia Prima</div>`,
      message: `
        <div style="border-bottom: 1px solid #e3e3e3;"> 
          <div class="col-sm-12 text-center floating-label enable-floating-label show-label">
              <label for="quantity">Cantidad a Entregar</label>
              <input type="number" class="form-control text-center" id="quantity" name="quantity">
          </div>
        </div>
    `,
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

          if (store > quantity) {
            toastr.error("Cantidad a entregar mayor a existencias");
            return false;
          }
          
          store <= reserved ? (pending = reserved - store) : (pending = 0);
          let stored = quantity - store;

          stored < 0 ? stored = 0 : stored;
          pending < 0 ? pending = 0 : pending;

          if (pending > stored) {
            toastr.info('Pendiente mayor a cantidad a entregar');
            return false; 
          }

          sessionStorage.setItem("idMaterial", id_material);
          sessionStorage.setItem("referenceProduct", reference);
          sessionStorage.setItem("product", material);
          sessionStorage.setItem("idProgramming", id_programming);
          sessionStorage.setItem("stored", stored);
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
    dataStore["referenceProduct"] = sessionStorage.getItem("reference");
    dataStore["product"] = sessionStorage.getItem("product");
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
    let data = tblDeliverOP.fnGetData(row);

    let users = await searchData(`/api/usersStore/${data.id_programming}/${data.id_material}`);
    const rows = users.map((user, index) => `
                <tr>
                  <td>${index + 1}</td>
                  <td>${user.firstname}</td>
                  <td>${user.lastname}</td>
                  <td>${user.email}</td>
                  <td>
                    ${parseFloat(user.delivery_store).toLocaleString("es-CO", {
                      minimumFractionDigits: 0,
                      maximumFractionDigits: 2,
                    })}
                  </td>
                </tr>`
    ).join('');

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

  $(document).on('click', '.seeUPDTDeliverOC', async function (e) {
    e.preventDefault();

    storedDLVS = [];
    const row = $(this).closest("tr")[0];
    const data = tblDeliverOP.fnGetData(row);

    // Obtenemos los usuarios de forma asincrónica
    const users = await searchData(`/api/usersStore/${data.id_programming}/${data.id_material}`);

    storedDLVS = [...users];
    
    // Generamos las filas en una sola operación usando .map()
    const rows = users.map((user, index) => `
        <tr>
          <td>${index + 1}</td>
          <td>${user.firstname}</td>
          <td>${user.lastname}</td>
          <td>${user.email}</td>
          <td><input type="number" class="form-control text-center updateDLVS" id="delvStore-${user.id_user_store}" value="${user.delivery_store}"></td>
          </tr>`).join('');

    // Mostramos el mensaje con Bootbox
    bootbox.confirm({
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
                            <tbody>${rows}</tbody>
                        </table>
                    </div>
                </div> 
            </div>`,
      buttons: {
        confirm: {
          label: "OK",
          className: "btn-success",
        },
        cancel: {
          label: "Cancel",
          className: "btn-danger",
        },
      }, 
      callback: function (result) { 
        if (result) {
          saveDLVS();
        }
      },
      size: 'large',
      backdrop: true
    }); 

  });
  
  $(document).on('keyup', '.updateDLVS', function () {
    try {
      let id_user_store = $(this).attr("id").split("-")[1];

      let value = parseFloat(this.value);
      let arr = storedDLVS.find(item => item.id_user_store == id_user_store);
      
      if (isNaN(value) || value <= 0) {
        toastr.error('Ingrese todos los campos');
        $(`#${this.id}`).val(arr.delivery_store);
        return false;
      }   
      
      let distincArr = storedDLVS.filter(item => item.id_user_store != id_user_store);
      let sumValue = value;
      let reserved = parseFloat(arr.reserved);

      distincArr.forEach(item => {
        sumValue += parseFloat(item.delivery_store);
      });

      if (value != arr.delivery_store) {
        for (let i = 0; i < storedDLVS.length; i++) {
          if (storedDLVS[i].id_user_store == id_user_store) {
            let store;
            let store1 = parseFloat(storedDLVS[i].delivery_store) - value;

            store1 < 0 ? store = -1 * store1 : store = store1;

            let stored = parseFloat(storedDLVS[i].quantity_material) + store1;
            let pending = sumValue != reserved ? reserved - sumValue : reserved - value;

            stored < 0 ? stored = 0 : stored;
            pending < 0 ? (pending = 0) : (pending);

            if (pending > stored) {
              toastr.info('Pendiente mayor a cantidad a entregar');
              break;
            }

            storedDLVS[i].quantity_material = stored;
            storedDLVS[i].delivery_store = value;
            storedDLVS[i].delivery_pending = pending;
          }
        }
      }
    } catch (error) {
      console.log(error);
    }
  });
 
  const saveDLVS = () => {
    bootbox.confirm({
      title: "Confirmar",
      message:
        "¿Esta seguro de modificar la cantidad de MP a Entregar?.",
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
          $.ajax({
            type: "POST",
            url: "/api/saveDLVS",
            data: { data: storedDLVS },
            success: function (resp) {
              message(resp)
            }
          });
        }
      },
    });
  }
  
  const message = (data, op) => {
    const { success, error, info, message } = data;
    if (success) { 
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
