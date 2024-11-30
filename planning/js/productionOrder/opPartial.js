$(document).ready(function () {
  //Datatable para parciales entregados desde la OP a Almacen
  loadTblPartialsDelivery = (id_programming) => {
    tblPartialsDelivery = $("#tblPartialsDelivery").DataTable({
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
          title: "Fecha Ejecución",
          data: "creation_date",
          className: "uniqueClassName dt-head-center",
          width: "200px",
          render: function (data, type, full, meta) {
            return moment(data).format("DD/MM/YYYY hh:mm A");
          },
        },
        {
          title: "Fechas de Producción",
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
          title: "Operador",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: function (data, type, row) {
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
          title: "Costo MO",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: function (data, type, row) {
            // Convierte las fechas a objetos Date
            const startDate = new Date(data.start_date);
            const endDate = new Date(data.end_date);

            // Obtiene la diferencia en milisegundos
            const differenceInMs = endDate - startDate;

            // Convierte la diferencia de milisegundos a minutos
            const minutes = Math.floor(differenceInMs / 1000 / 60);
            let cost_payroll = parseFloat(data.minute_value) * minutes;

            return `$${cost_payroll.toLocaleString("es-CO", {
              minimumFractionDigits: 0,
              maximumFractionDigits: 0,
            })}`;
          },
        },
        {
          title: "Costo Maquina",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: function (data, type, row) {
            // Convierte las fechas a objetos Date
            const startDate = new Date(data.start_date);
            const endDate = new Date(data.end_date);

            // Obtiene la diferencia en milisegundos
            const differenceInMs = endDate - startDate;

            // Convierte la diferencia de milisegundos a minutos
            const minutes = Math.floor(differenceInMs / 1000 / 60);
            let cost_machine = parseFloat(data.minute_depreciation) * minutes;

            return `$${cost_machine.toLocaleString("es-CO", {
              minimumFractionDigits: 0,
              maximumFractionDigits: 0,
            })}`;
          },
        },
        {
          title: "Acciones",
          data: null,
          className: "uniqueClassName dt-head-center",
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
        //Calcula totales de columnas específicas
        let totalDefectiveUnits = 0;
        let totalDeliveredQuantity = 0;
        let totalCostPayroll = 0;
        let totalCostIndirect = 0;

        //Suma los valores de columnas
        data.forEach(function (row) {
          totalDefectiveUnits += parseFloat(row.waste) || 0;
          totalDeliveredQuantity += parseFloat(row.partial_quantity) || 0;

          // Calcula el costo de la nómina para cada fila
          const startDate = new Date(row.start_date);
          const endDate = new Date(row.end_date);
          const differenceInMs = endDate - startDate;
          const minutes = Math.floor(differenceInMs / 1000 / 60);

          const cost_payroll = parseFloat(row.minute_value) * minutes || 0;
          const cost_indirect =
            parseFloat(row.minute_depreciation) * minutes || 0;

          totalCostPayroll += cost_payroll;
          totalCostIndirect += cost_indirect;
        });

        // Actualizar el contenido del footer con los totales calculados
        $('#totalDefectiveUnits').html(
          `${$.fn.dataTable.render
            .number(".", ",", 0, "")
            .display(totalDefectiveUnits)} Und`
        );
        $('#totalDeliveredQuantity').html(
          `${$.fn.dataTable.render
            .number(".", ",", 0, "")
            .display(totalDeliveredQuantity)} Und`
        );
        $('#totalCostPayroll').html(
          `$${$.fn.dataTable.render
            .number(".", ",", 0, "")
            .display(totalCostPayroll)}`
        );
        $('#totalCostIndirect').html(
          `$${$.fn.dataTable.render
            .number(".", ",", 0, "")
            .display(totalCostIndirect)}`
        );
      },
    });
  };

  /* Crear OP Parcial */
  $("#btnDeliverPartialOP").click(function (e) {
    e.preventDefault();

    // Selecciona los inputs de fecha y hora
    const startDateTimeInput = document.getElementById("startDateTime");
    const endDateTimeInput = document.getElementById("endDateTime");

    const startDateTime = new Date(startDateTimeInput.value);
    const endDateTime = new Date(endDateTimeInput.value);

    if (endDateTime < startDateTime) {
      // Notifica mensaje de error
      toastr.error(
        "La fecha y hora de finalización no puede ser menor a la fecha y hora de inicio."
      );

      // Vacía los campos
      startDateTimeInput.value = "";
      endDateTimeInput.value = "";
      return false;
    }

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
    let row = $(this).closest('tr');
    let data = $('#tblPartialsDelivery').DataTable().row(row).data();

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

    let dataOPP = $('#tblPartialsDelivery').DataTable().rows().data().toArray();

    let formattedStartDate = formatDateTime(startDateTime);
    let formattedEndDate = formatDateTime(endDateTime);

    let objStartDate = dataOPP.find(item=> item.start_date == formattedStartDate);
    let objEndDate = dataOPP.find(item=> item.end_date == formattedEndDate);

    if (objStartDate || objEndDate) {
      toastr.error("Fecha de inicio o finalizacion ya ingresada");
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
    let row = $(this.activeElement).parent().parent()[0];
    let data = $('#tblPartialsDelivery').DataTable().row(row).data();

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

  const formatDateTime = (date) => {
    let parsedDate = new Date(date);

    // Formatear la fecha
    let year = parsedDate.getFullYear();
    let month = String(parsedDate.getMonth() + 1).padStart(2, "0"); // Meses empiezan en 0
    let day = String(parsedDate.getDate()).padStart(2, "0");

    // Formatear la hora
    let hours = String(parsedDate.getHours()).padStart(2, "0");
    let minutes = String(parsedDate.getMinutes()).padStart(2, "0");
    let seconds = String(parsedDate.getSeconds()).padStart(2, "0");

    // Combinar en el formato deseado
    let formattedDate = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
    return formattedDate;
  };

  /* Mensaje de exito */

  const messageOPPartial = (data) => {
    const { success, error, info, message } = data;

    sessionStorage.removeItem("id_part_deliv");
    let id_programming = sessionStorage.getItem('id_programming');

    if (success) {
      $("#formAddOPPArtial").trigger("reset");
      toastr.success(message);
      loadTblPartialsDelivery(id_programming);
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };
});
