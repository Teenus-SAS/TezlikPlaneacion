$(document).ready(function () {
  //Datatable para parciales entregados desde la OP a Almacen
  loadTblPartialsDelivery = (id_programming, visible) => {
    tblPartialsDelivery = $("#tblPartialsDelivery").dataTable({
      destroy: true,
      dom: "t",
      paging: false,
      info: false,
      searching: false,
      ajax: {
        url: `/api/productionOrderPartial/${id_programming}`,
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
              "DD/MM/YYYY HH:mm A"
            )}<br>Fin: ${moment(end_date).format("DD/MM/YYYY HH:mm A")}`;
          },
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
          render: function (data, type, row) {
            // Utiliza DataTable render para el formato del número y después concatena " UND"
            const formattedNumber = $.fn.dataTable.render
              .number(".", ",", 0, "")
              .display(data);
            return `${formattedNumber} Und`;
          },
        },
        {
          title: "Cantidad Entregada",
          data: "partial_quantity",
          className: "uniqueClassName dt-head-center",
          render: function (data, type, row) {
            // Utiliza DataTable render para el formato del número y después concatena " UND"
            const formattedNumber = $.fn.dataTable.render
              .number(".", ",", 0, "")
              .display(data);
            return `${formattedNumber} Und`;
          },
        },
        {
          title: "Fecha Creacion",
          data: "creation_date",
          className: "uniqueClassName dt-head-center",
          width: "200px",
          render: function (data, type, full, meta) {
            return moment(data).format("DD/MM/YYYY HH:mm A");
          },
        },
        {
          title: "Acciones",
          data: null,
          className: "uniqueClassName dt-head-center",
          visible: visible,
          render: function (data) {
            let action;
            if (!data.receive_date || data.receive_date == "0000-00-00") {
              action = `<a href="javascript:;" <i id="upd-${data.id_part_deliv}" class="bx bx-edit-alt updateOPPartial" data-toggle='tooltip' title='Actualizar Produccion' style="font-size: 30px;"></i></a>
                            <a href="javascript:;" <i id="${data.id_part_deliv}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Produccion' style="font-size: 30px;color:red" onclick="deleteOPPartialFunction()"></i></a>`;
            } else {
              action = `Recibido: <br>${data.firstname_receive} ${data.lastname_receive}<br>${data.receive_date}`;
            }
            return action;
          },
        },
      ],
      footerCallback: function (row, data, start, end, display) {
        // Calcular totales de columnas específicas
        let totalDefectiveUnits = 0;
        let totalDeliveredQuantity = 0;

        // Iterar a través de todas las filas visibles para sumar los valores de columnas
        data.forEach(function (row) {
          totalDefectiveUnits += parseFloat(row.waste) || 0; // Suma de unidades defectuosas
          totalDeliveredQuantity += parseFloat(row.partial_quantity) || 0; // Suma de cantidad entregada
        });

        // Actualizar el contenido del footer con los totales calculados
        $(this.api().column(3).footer()).html(
          `${$.fn.dataTable.render
            .number(".", ",", 0, "")
            .display(totalDefectiveUnits)} Und`
        );
        $(this.api().column(4).footer()).html(
          `${$.fn.dataTable.render
            .number(".", ",", 0, "")
            .display(totalDeliveredQuantity)} Und`
        );
      },
    });
    // Retorna el DataTable creado y los valores calculados
    localStorage.setItem("totalDefectiveUnits", totalDefectiveUnits);
    localStorage.setItem("totalDeliveredQuantity", totalDeliveredQuantity);
  };

  /* Crear OP Parcial */
  $("#btnDeliverPartialOP").click(function (e) {
    e.preventDefault();

    const idPartDeliv = sessionStorage.getItem("id_part_deliv") || null;
    const apiUrl = !idPartDeliv ? "/api/addOPPartial" : "/api/updateOPPartial";

    checkDataOPPartial(apiUrl, idPartDeliv);
  });

  /* Actualizar OP Parcial */
  $(document).on("click", ".updateOPPartial", function (e) {
    $("#btnDeliverPartialOP").text("Actualizar");

    // Obtener el ID del elemento
    const idPartDeliv = $(this).attr("id").split("-")[1];

    sessionStorage.setItem("id_part_deliv", idPartDeliv);

    // Obtener data
    const row = $(this).closest("tr")[0];
    const data = tblPartialsDelivery.fnGetData(row);

    // Asignar valores a los campos del formulario y animar
    $("#startDateTime").val(data.start_date);
    $("#endDateTime").val(data.end_date);
    $("#waste").val(data.waste);
    $("#quantityProduction").val(data.partial_quantity);
  });

  // Entregas Parciales
  const checkDataOPPartial = async (url, idPartDeliv) => {
    let startDateTime = $("#startDateTime").val();
    let endDateTime = $("#endDateTime").val();
    // let operator = parseInt($('#operator').val());
    let waste = parseInt($("#waste").val());
    let quantityProduction = parseInt($("#quantityProduction").val());

    if (
      !startDateTime ||
      startDateTime == "" ||
      !endDateTime ||
      endDateTime == "" ||
      isNaN(quantityProduction) ||
      quantityProduction <= 0
    ) {
      toastr.error("Ingrese todos los campos");
      return false;
    }

    let id_programming = sessionStorage.getItem("id_programming");

    let dataOP = new FormData(formAddOPPArtial);
    dataOP.append("idProgramming", id_programming);

    if (idPartDeliv) dataOP.append("idPartDeliv", idPartDeliv);

    let resp = await sendDataPOST(url, dataOP);

    messageOPPartial(resp);
  };

  /* Eliminar productos */
  deleteOPPartialFunction = () => {
    const row = $(this.activeElement).closest("tr")[0];
    const data = tblPartialsDelivery.fnGetData(row);

    const { id_part_deliv } = data;

    bootbox.confirm({
      title: "Eliminar",
      message:
        "Está seguro de eliminar esta programacion? Esta acción no se puede reversar.",
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
          $.get(
            `/api/deleteOPPartial/${id_part_deliv}`,
            function (data, textStatus, jqXHR) {
              messageOPPartial(data);
            }
          );
        }
      },
    });
  };

  /* Mensaje de exito */

  const messageOPPartial = (data) => {
    const { success, error, info, message } = data;

    sessionStorage.removeItem("id_part_deliv");

    if (success) {
      $("#formAddOPPArtial").trigger("reset");
      toastr.success(message);
      loadAllDataPO();
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };
});
