$(document).ready(function () {
  $(".selectNavigation").click(function (e) {
    e.preventDefault();

    if (this.id == "pending") loadTblRequisitions(pending, true);
    else if (this.id == "done") loadTblRequisitions(done, false);
  });

  loadAllData = async (op, min_date, max_date) => {
    try {
      const [dataRequisitions, dataStock, dateRequisitions] = await Promise.all(
        [
          searchData("/api/requisitions"),
          searchData("/api/rMStock"),
          op == 3
            ? searchData(`/api/requisitions/${min_date}/${max_date}`)
            : null,
        ]
      );

      sessionStorage.setItem("stock", JSON.stringify(dataStock));

      let card = document.getElementsByClassName("selectNavigation");

      if (card[0].className.includes("active")) {
        pending = 1;
        op = 1;
      } else {
        pending = 1;
        op = 2;
      }

      pending = dataRequisitions
        .filter(
          (item) =>
            item.application_date == "0000-00-00" &&
            item.delivery_date == "0000-00-00" &&
            item.purchase_order == ""
        )
        .map((item) => ({ ...item, status: "Pendiente" }));

      let done1 = dataRequisitions
        .filter(
          (item) =>
            item.application_date != "0000-00-00" &&
            item.delivery_date != "0000-00-00" &&
            item.purchase_order != "" &&
            item.admission_date
        )
        .map((item) => ({ ...item, status: "Recibido" }));

      let date = formatDate(new Date());

      let process = dataRequisitions
        .filter(
          (item) =>
            item.application_date != "0000-00-00" &&
            item.delivery_date != "0000-00-00" &&
            item.purchase_order != "" &&
            !item.admission_date &&
            item.delivery_date >= date
        )
        .map((item) => ({ ...item, status: "Proceso" }));

      let process1 = dataRequisitions.filter(
        (item) =>
          item.application_date != "0000-00-00" &&
          item.delivery_date != "0000-00-00" &&
          item.purchase_order != "" &&
          !item.admission_date
      );

      let delayed = process1
        .filter((item) => item.delivery_date < date)
        .map((item) => ({ ...item, status: "Retrasada" }));

      done = [...delayed, ...process, ...done1];

      $("#lblPending").html(` Pendientes: ${pending.length}`);
      $("#lblProcess").html(` Procesado: ${process.length}`);
      $("#lblDelayed").html(` Retrasadas: ${delayed.length}`);
      $("#lblReceived").html(` Recibido: ${done1.length}`);

      let visible = true;
      if (op === 1) dataToLoad = pending;
      else if (op === 2) {
        dataToLoad = done;
        visible = false;
      } else {
        if (pending == 1)
          dataToLoad = dateRequisitions.filter(
            (item) =>
              item.application_date == "0000-00-00" &&
              item.delivery_date == "0000-00-00" &&
              item.purchase_order == ""
          );
        else
          dataToLoad = dateRequisitions.filter(
            (item) =>
              item.application_date != "0000-00-00" &&
              item.delivery_date != "0000-00-00" &&
              item.purchase_order != ""
          );
      }

      if (dataToLoad) {
        loadTblRequisitions(dataToLoad, visible);
      }
    } catch (error) {
      console.error("Error loading data:", error);
    }
  };

  /* Cargue tabla de Productos Materiales */
  loadTblRequisitions = (data, visible) => {
    if ($.fn.dataTable.isDataTable("#tblRequisitions")) {
        // Actualizar el título de la columna
        let table = $("#tblRequisitions").DataTable();
        // let column = table.column(10); // Asumiendo que la columna de "Fecha Entrega/Recibido" es la décima (índice 10)
        // column.header().textContent = title_delivery_date;
        
        // Actualizar los datos en la tabla
        table.clear();
        table.rows.add(data).draw();
        return;
    } 

    tblRequisitions = $("#tblRequisitions").dataTable({ 
      pageLength: 50,
      order: [[0, "asc"]],
      data: data,
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
          title: "Acciones",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: renderRequisitionActions,
        },

        {
          title: "Referencia",
          data: "reference",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Descripción",
          data: "material",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Medida",
          data: "abbreviation",
          className: "uniqueClassName dt-head-center",
        },

        {
          title: "Proveedor Sugerido",
          data: "provider",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Cant. Requerida",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: function (data) {
            let quantity = data.quantity_required;

            if (data.abbreviation === "UND")
              quantity = Math.floor(quantity).toLocaleString("es-CO", {
                maximumFractionDigits: 0,
              });
            else
              quantity = quantity.toLocaleString("es-CO", {
                minimumFractionDigits: 2,
              });

            return quantity;
          },
        },
        {
          title: "Cant. Solicitada",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: function (data) {
            let quantity = data.quantity_requested;

            if (data.abbreviation === "UND")
              quantity = Math.floor(quantity).toLocaleString("es-CO", {
                maximumFractionDigits: 0,
              });
            else
              quantity = quantity.toLocaleString("es-CO", {
                minimumFractionDigits: 2,
              });

            return quantity;
          },
        },
        {
          title: "Fecha Solicitud",
          data: "application_date",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Estado",
          data: "status",
          className: "uniqueClassName dt-head-center",
          render: renderRequisitionStatus,
        },
        {
          title: 'Fecha',
          data: null,
          className: "uniqueClassName dt-head-center",
          render: function (data) { 
            let delivery_date = data.delivery_date;
            let status = data.status;
            let nameDate = 'Fecha Entrega';

            if (status == 'Recibido')
              nameDate = 'Fecha Recibido';

            return `<a href="javascript:;"><i title="${nameDate}" style="color:black;">${delivery_date}</i></a>`;
          }
        },
        {
          title: "Orden de Compra",
          data: "purchase_order",
          className: "uniqueClassName dt-head-center",
        },
      ],
      footerCallback: function (row, data, start, end, display) {
        let quantity_required = 0;
        let quantity_requested = 0;

        for (i = 0; i < display.length; i++) {
          quantity_required += parseFloat(data[display[i]].quantity_required);
          quantity_requested += parseFloat(data[display[i]].quantity_requested);
        }

        $(this.api().column(6).footer()).html(
          quantity_required.toLocaleString("es-CO", {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
          })
        );
        $(this.api().column(7).footer()).html(
          quantity_requested.toLocaleString("es-CO", {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
          })
        );
      },
    });
  };

  loadAllData(1, null, null);

  function renderRequisitionStatus(data, type, full, meta) {
    let badge = "";
    if (data == "Pendiente") badge = "badge-info";
    else if (data == "Proceso") badge = "badge-warning";
    else if (data == "Retrasada") badge = "badge-danger";
    else if (data == "Recibido") badge = "badge-success";

    return `<span class="badge ${badge}">${data}</span>`;
  }

  function renderRequisitionActions(data) {
    let action = "";
    if (data.status != "Recibido") {
      action = `<a href="javascript:;" <i id="upd-${data.id_requisition}" class="bx bx-edit-alt updateRequisition" data-toggle='tooltip' title='Actualizar Requisicion' style="font-size: 30px;"></i></a>
              <a href="javascript:;" <i id="${data.id_requisition}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Requisicion' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
    } else {
      action = data.admission_date;
    }
    return action;
  }
});
