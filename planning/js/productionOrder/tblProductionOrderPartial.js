$(document).ready(function () {
  op_to_store == '1' ? visible = true : visible = false;

  // Productos
  tblPartialsDeliveryPT = $("#tblPartialsDeliveryPT").dataTable({
    destroy: true,
    autoWidth: false,
    pageLength: 50,
    fixedColumns: {
      leftColumns: 1,
      rightColumns: 1,
    },
    ajax: {
      url: `/api/productionOrderPartial`,
      dataSrc: "",
    },
    language: {
      url: "/assets/plugins/i18n/Spanish.json",
    },
    columns: [
      {
        title: "No.",
        data: null,
        className: "uniqueClassName dt-head-center",
        render: function (data, type, full, meta) {
          return meta.row + 1;
        },
      },
      {
        title: "Fechas",
        data: null,
        className: "uniqueClassName dt-head-center",
        width: "200px",
        render: function (data, type, full, meta) {
          const start_date = full.start_date;
          const end_date = full.end_date;

          return `Inicio: ${moment(start_date).format(
            "DD/MM/YYYY hh:mm A"
          )}<br>Fin: ${moment(end_date).format("DD/MM/YYYY hh:mm A")}`;
        },
      },
      {
        title: "Referencia",
        data: "reference",
        className: "uniqueClassName dt-head-center",
      },
      {
        title: "Producto",
        data: "product",
        className: "uniqueClassName dt-head-center",
      },
      {
        title: "Operador",
        data: null,
        className: "uniqueClassName dt-head-center",
        render: function (data) {
          return `${data.firstname} ${data.lastname}`;
        },
      },
      {
        title: "Unidades Defectuosas",
        data: "waste",
        className: "uniqueClassName dt-head-center",
        render: $.fn.dataTable.render.number(".", ",", 0, ""),
      },
      {
        title: "Cantidad Entregada",
        data: "partial_quantity",
        className: "uniqueClassName dt-head-center",
        render: $.fn.dataTable.render.number(".", ",", 0, ""),
      },
      {
        title: "Acción",
        data: null,
        className: "uniqueClassName dt-head-center",
        visible: visible,
        render: function (data) {
          if (!data.receive_date || data.receive_date == "0000-00-00")
            action = `<button class="btn btn-info changeDateOPPT">Recibir OP</button>`;
          else {
            action = `Recibido: <br>${data.firstname_receive} ${data.lastname_receive}<br>${data.receive_date}`;
          }

          return action;
        },
      },
    ],
    headerCallback: (thead) => {
      $(thead).find("th").css({
        "background-color": "#386297",
        color: "white",
        "text-align": "center",
        "font-weight": "bold",
        padding: "10px",
        border: "1px solid #ddd",
      });
    },
    rowGroup: {
      dataSrc: function (row) {
        return `<th class="text-center" colspan="8" style="font-weight: bold;"> Orden Produccion - ${row.num_production} </th>`;
      },
      startRender: function (rows, group) {
        return $("<tr/>").append(group);
      },
      className: "odd",
    },
  });

  // Recibir OP
  $(document).on("click", ".changeDateOPPT", function (e) {
    e.preventDefault();

    let date = new Date().toISOString().split("T")[0];
    const row = $(this).closest("tr")[0];
    let data = tblPartialsDeliveryPT.fnGetData(row);

    bootbox.confirm({
      title: "Ingrese Fecha De Ingreso!",
      message: `<div class="col-sm-12 floating-label enable-floating-label">
                        <input class="form-control" type="date" name="date" id="dateOP" max="${date}"></input>
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
          let date = $("#dateOP").val();

          if (!date) {
            toastr.error("Ingrese los campos");
            return false;
          }

          let form = new FormData();
          form.append("idPartDeliv", data.id_part_deliv);
          form.append("idProduct", data.id_product);
          form.append("idOrder", data.id_order);
          form.append("origin", data.origin);
          form.append(
            "quantity",
            parseFloat(data.quantity_product) +
              parseFloat(data.partial_quantity)
          );
          form.append("date", date);

          $.ajax({
            type: "POST",
            url: "/api/saveReceiveOPPTDate",
            data: form,
            contentType: false,
            cache: false,
            processData: false,
            success: function (resp) {
              messageOPPT(resp);
            },
          });
        }
      },
    });
  });

  const messageOPPT = (data) => {
    const { success, error, info, message } = data;
    if (success) {
      toastr.success(message);
      updateTablePT();
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };

  function updateTablePT() {
    $("#tblPartialsDeliveryPT").DataTable().clear();
    $("#tblPartialsDeliveryPT").DataTable().ajax.reload();
  }

  // Materiales
  tblPartialsDeliveryMP = $("#tblPartialsDeliveryMP").dataTable({
    autoWidth: false,
    destroy: true,
    pageLength: 50,
    fixedColumns: {
      leftColumns: 1,
      rightColumns: 1,
    },

    ajax: {
      url: `/api/productionOrderMaterial`,
      dataSrc: "",
    },
    language: {
      url: "/assets/plugins/i18n/Spanish.json",
    },
    columns: [
      {
        title: "No.",
        data: null,
        className: "uniqueClassName dt-head-center",
        render: function (data, type, full, meta) {
          return meta.row + 1;
        },
      },
      {
        title: "Referencia",
        data: "reference",
        className: "uniqueClassName dt-head-center",
      },
      {
        title: "Material",
        data: "material",
        className: "uniqueClassName dt-head-center",
      },
      {
        title: "Cantidad",
        data: "quantity",
        className: "uniqueClassName dt-head-center",
        render: $.fn.dataTable.render.number(".", ",", 0, ""),
      },
      {
        title: "Medida",
        data: "abbreviation",
        className: "uniqueClassName dt-head-center",
      },
      {
        title: "Acciones",
        data: null,
        className: "uniqueClassName dt-head-center",
        // visible: visible,
        render: function (data) {
          if (!data.receive_date || data.receive_date == "0000-00-00")
            action = `<button class="btn btn-info changeDateOPMP">Aceptar MP</button>`;
          else {
            action = `Recibido: <br>${data.firstname_receive} ${data.lastname_receive}<br>${data.receive_date}`;
          }

          return action;
        },
      },
    ],
    headerCallback: (thead) => {
      $(thead).find("th").css({
        "background-color": "#386297",
        color: "white",
        "text-align": "center",
        "font-weight": "bold",
        padding: "10px",
        border: "1px solid #ddd",
      });
    },
    rowGroup: {
      dataSrc: function (row) {
        return `<th class="text-center" colspan="8" style="font-weight: bold;"> Orden Produccion - ${row.num_production} </th>`;
      },
      startRender: function (rows, group) {
        return $("<tr/>").append(group);
      },
      className: "odd",
    },
  });

  // Recibir OP
  $(document).on("click", ".changeDateOPMP", function (e) {
    e.preventDefault();

    let date = new Date().toISOString().split("T")[0];
    const row = $(this).closest("tr")[0];
    let data = tblPartialsDeliveryMP.fnGetData(row);

    bootbox.confirm({
      title: "Ingrese Fecha De Ingreso!",
      message: `<div class="col-sm-12 floating-label enable-floating-label">
                        <input class="form-control" type="date" name="date" id="dateOPMP" max="${date}"></input>
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
          let date = $("#dateOPMP").val();

          if (!date) {
            toastr.error("Ingrese los campos");
            return false;
          }

          let form = new FormData();
          form.append("idOPM", data.id_prod_order_material);
          form.append("idMaterial", data.id_material);
          form.append("referenceProduct", data.reference);
          form.append("product", data.material);
          form.append(
            "quantity",
            parseFloat(data.quantity_material) - parseFloat(data.quantity)
          );
          form.append("date", date);

          $.ajax({
            type: "POST",
            url: "/api/saveReceiveOPMPDate",
            data: form,
            contentType: false,
            cache: false,
            processData: false,
            success: function (resp) {
              messageOPMP(resp);
            },
          });
        }
      },
    });
  });

  const messageOPMP = (data) => {
    const { success, error, info, message } = data;
    if (success) {
      toastr.success(message);
      updateTableMP();
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };

  function updateTableMP() {
    $("#tblPartialsDeliveryMP").DataTable().clear();
    $("#tblPartialsDeliveryMP").DataTable().ajax.reload();
  }
});
