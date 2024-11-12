$(document).ready(function () {
  // $(document).on("click", ".changeDate", function (e) {
  //   e.preventDefault();

  //   let date = new Date().toISOString().split("T")[0];
  //   const row = $(this).closest("tr")[0];
  //   let data = tblOffices.fnGetData(row);

  //   bootbox.confirm({
  //     title: "Ingrese Fecha De Entrega!",
  //     message: `<div class="col-sm-12 floating-label enable-floating-label">
  //                       <input class="form-control" type="date" name="date" id="dateOFF" max="${date}"></input>
  //                       <label for="date">Fecha</span></label>
  //                     </div>`,
  //     buttons: {
  //       confirm: {
  //         label: "Agregar",
  //         className: "btn-success",
  //       },
  //       cancel: {
  //         label: "Cancelar",
  //         className: "btn-danger",
  //       },
  //     },
  //     callback: function (result) {
  //       if (result) {
  //         let date = $("#dateOFF").val();

  //         if (!date) {
  //           toastr.error("Ingrese los campos");
  //           return false;
  //         }

  //         let form = new FormData();
  //         form.append("idOrder", data.id_order);
  //         form.append("idProduct", data.id_product);
  //         form.append("originalQuantity", data.original_quantity);
  //         form.append("quantity", data.quantity);
  //         form.append("stock", data.minimum_stock);
  //         form.append("date", date);

  //         $.ajax({
  //           type: "POST",
  //           url: "/api/changeOffices",
  //           data: form,
  //           contentType: false,
  //           cache: false,
  //           processData: false,
  //           success: function (resp) {
  //             message(resp);
  //           },
  //         });
  //       }
  //     },
  //   });
  // });
  $(document).on("click", ".changeDate", function (e) {
    e.preventDefault();

    // Obtener la fecha actual y los datos de la fila correspondiente
    // let date = new Date().toISOString().split("T")[0];
    let date = new Date();
    let formattedDate = date.getFullYear() + '-' +
      String(date.getMonth() + 1).padStart(2, '0') + '-' +
      String(date.getDate()).padStart(2, '0');

    const row = $(this).closest("tr")[0];
    let data = tblOffices.fnGetData(row);

    bootbox.confirm({
      title: "Ingrese Fecha De Entrega!",
      message: `<div class="col-sm-12 floating-label enable-floating-label">
                        <input class="form-control" type="date" name="date" id="dateOFF" max="${formattedDate}"></input>
                        <label for="date">Fecha</label>
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
          let selectedDate = $("#dateOFF").val();

          // Validar que se haya seleccionado una fecha
          if (!selectedDate) {
            toastr.error("Ingrese los campos");
            return false;
          }

          let form = new FormData();
          form.append("idOrder", data.id_order);
          form.append("idProduct", data.id_product);
          form.append("origin", data.origin);
          form.append("originalQuantity", data.original_quantity);
          form.append("quantity", data.quantity);
          form.append("stock", data.minimum_stock);
          form.append("date", selectedDate);

          $.ajax({
            type: "POST",
            url: "/api/changeOffices",
            data: form,
            contentType: false,
            cache: false,
            processData: false,
            success: function (resp) {
              // Muestra un mensaje de éxito o el mensaje recibido del servidor
              message(resp);
              toastr.success("Fecha de entrega actualizada correctamente");
            },
            error: function (xhr, status, error) {
              // Muestra un mensaje de error si la solicitud falla
              toastr.error("Error al actualizar la fecha de entrega. Intente nuevamente.");
            },
          });
        }
      },
    });
  });

  $(".cardSearchDate").hide();

  $("#btnOpenSearchDate").click(function (e) {
    e.preventDefault();

    $(".cardSearchDate").toggle(800);
    $("#formSearchDate").trigger("reset");
    let date = new Date().toISOString().split("T")[0];

    $("#lastDate").val(date);

    let maxDate = document.getElementById("lastDate");
    let minDate = document.getElementById("firtsDate");

    maxDate.setAttribute("max", date);
    minDate.setAttribute("max", date);
  });

  $("#btnSearchDate").click(async function (e) {
    e.preventDefault();

    let firtsDate = $("#firtsDate").val();
    let lastDate = $("#lastDate").val();

    if (!firtsDate || firtsDate == "" || !lastDate || lastDate == "") {
      toastr.error("Ingrese los campos");
      return false;
    }

    loadAllData(3, firtsDate, lastDate);
  });

  // $(document).on("click", ".cancelOrder", function (e) {
  //   e.preventDefault();

  //   const row = $(this).closest("tr")[0];
  //   let data = tblOffices.fnGetData(row);

  //   bootbox.confirm({
  //     title: "Cancelar Despacho",
  //     message: `Está seguro de cancelar este despacho? Esta acción no se puede reversar.`,
  //     buttons: {
  //       confirm: {
  //         label: "Si",
  //         className: "btn-success",
  //       },
  //       cancel: {
  //         label: "No",
  //         className: "btn-danger",
  //       },
  //     },
  //     callback: function (result) {
  //       if (result) {
  //         let form = new FormData();
  //         form.append("idOrder", data.id_order);
  //         form.append("idProduct", data.id_product);
  //         form.append("originalQuantity", data.original_quantity);
  //         form.append("quantity", data.accumulated_quantity);

  //         $.ajax({
  //           type: "POST",
  //           url: "/api/cancelOffice",
  //           data: form,
  //           contentType: false,
  //           cache: false,
  //           processData: false,
  //           success: function (resp) {
  //             message(resp);
  //           },
  //         });
  //       }
  //     },
  //   });
  // });

  $(document).on("click", ".cancelOrder", function (e) {
    e.preventDefault();

    // Obtener la fila y los datos correspondientes
    const row = $(this).closest("tr")[0];
    let data = tblOffices.fnGetData(row);

    bootbox.confirm({
      title: "Cancelar Despacho",
      message: `Está seguro de cancelar este despacho? Esta acción no se puede reversar.`,
      buttons: {
        confirm: {
          label: "Sí",
          className: "btn-success",
        },
        cancel: {
          label: "No",
          className: "btn-danger",
        },
      },
      callback: function (result) {
        if (result) {
          let form = new FormData();
          form.append("idOrder", data.id_order);
          form.append("idProduct", data.id_product);
          form.append("originalQuantity", data.original_quantity);
          form.append("quantity", data.accumulated_quantity);

          $.ajax({
            type: "POST",
            url: "/api/cancelOffice",
            data: form,
            contentType: false,
            cache: false,
            processData: false,
            success: function (resp) {
              // Mensaje de éxito
              toastr.success("El despacho ha sido cancelado correctamente.");
              message(resp); // Puedes conservar esta línea si 'message' realiza otras funciones importantes
            },
            error: function (xhr, status, error) {
              // Manejo de error
              toastr.error("Hubo un error al cancelar el despacho. Intente nuevamente.");
            },
          });
        }
      },
    });
  });

  /* Mensaje de exito */
  message = (data) => {
    const { success, error, info, message } = data;
    if (success) { 
      loadAllData(1, null, null);
      $(".cardAddDate").hide(800);
      $("#formAddDate").trigger("reset");
      toastr.success(message);
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };
});
