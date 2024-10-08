$(document).ready(function () {
  $(".selectNavigation").click(function (e) {
    e.preventDefault();

    $(".cardAddRequisitions, .cardSearchDate, .cardImportRequisitions").hide(
      800
    );

    if (this.id == "pending") loadTblRequisitions(pending, false);
    else if (this.id == "done") loadTblRequisitions(done, true);
  });

  loadAllData = async (op, min_date, max_date) => {
    try {
      const [dataRequisitions, dataMPStock, dataPTStock, dateRequisitions] =
        await Promise.all([
          searchData("/api/requisitions"),
          searchData("/api/rMStock"),
          searchData("/api/pStock"),
          op == 3
            ? searchData(`/api/requisitions/${min_date}/${max_date}`)
            : null,
        ]);

      sessionStorage.setItem("MPStock", JSON.stringify(dataMPStock));
      sessionStorage.setItem("PTStock", JSON.stringify(dataPTStock));

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

      let visible = false;
      if (op === 1) dataToLoad = pending;
      else if (op === 2) {
        dataToLoad = done;
        visible = true;
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
    tblRequisitions = $("#tblRequisitions").dataTable({
      fixedHeader: true,
      scrollY: "400px",
      scrollCollapse: true,
      destroy: true,
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
          title: "Fecha Creacion",
          data: "creation_date",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Fecha Solicitud",
          data: "application_date",
          className: "uniqueClassName dt-head-center",
          visible: visible,
        },
        {
          title: "Fecha Entrega",
          data: null,
          className: "uniqueClassName dt-head-center",
          visible: visible,
          render: function (data) {
            let delivery_date = data.delivery_date;
            let status = data.status;
            let nameDate = "Fecha Entrega";

            if (status == "Recibido") nameDate = "Fecha Recibido";

            return `<a href="javascript:;"><i title="${nameDate}" style="color:black;">${delivery_date}</i></a>`;
          },
        },
        {
          title: "Pedido",
          data: "num_order",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Referencia",
          data: "reference",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Descripción",
          data: "description",
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
            let quantity = parseFloat(data.quantity_required);

            if (data.abbreviation === "UND")
              quantity = Math.floor(quantity).toLocaleString("es-CO", {
                maximumFractionDigits: 0,
              });
            else
              quantity = quantity.toLocaleString("es-CO", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
              });

            return `${quantity} ${data.abbreviation}`;
          },
        },
        {
          title: "Cant. Solicitada",
          data: null,
          visible: visible,
          className: "uniqueClassName dt-head-center",
          render: function (data) {
            let quantity = parseFloat(data.quantity_requested);

            if (data.abbreviation === "UND")
              quantity = Math.floor(quantity).toLocaleString("es-CO", {
                maximumFractionDigits: 0,
              });
            else
              quantity = quantity.toLocaleString("es-CO", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
              });

            return `${quantity} ${data.abbreviation}`;
          },
        },
        {
          title: "Estado",
          data: "status",
          className: "uniqueClassName dt-head-center",
          render: renderRequisitionStatus,
        },

        {
          title: "Orden de Compra",
          data: "purchase_order",
          className: "uniqueClassName dt-head-center",
          visible: visible,
        },
        {
          title: "Ejecutado Por",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: (data) =>
            `${data.firstname_requisition} ${data.lastname_requisition}`,
          visible: visible,
        },
        {
          title: "Acciones",
          data: null,
          className: "uniqueClassName dt-head-center p-0",
          visible: (data) => (data.status != "Recibido" ? true : false),
          render: (data) => renderRequisitionActions(data, visible),
        },
      ],
      headerCallback: function (thead, data, start, end, display) {
        $(thead).find("th").css({
          "background-color": "#386297",
          color: "white",
          "text-align": "center",
          "font-weight": "bold",
          padding: "10px",
          border: "1px solid #ddd",
        });
      },
      footerCallback: function (row, data, start, end, display) {
        let quantity_required = 0;
        let quantity_requested = 0;

        for (i = 0; i < display.length; i++) {
          quantity_required += parseFloat(data[display[i]].quantity_required);
          quantity_requested += parseFloat(data[display[i]].quantity_requested);
        }

        $("#lblTotalQRequired").html(
          quantity_required.toLocaleString("es-CO", {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
          })
        );
        $("#lblTotalQRequested").html(
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
    const badgeClasses = {
      Pendiente: "badge-info",
      Proceso: "badge-warning",
      Retrasada: "badge-danger",
      Recibido: "badge-success",
    };

    // seleccionar la clase
    const badge = badgeClasses[data] || "badge-secondary";

    return `<span class="badge ${badge}">${data}</span>`;
  }

  function renderRequisitionActions(data, visible) {
    let action = "";
    if (data.status != "Recibido" && data.status != "Proceso") {
      let id, className1, className2;

      if (!data.id_requisition_material) {
        id = data.id_requisition_product;
        className1 = "id_requisition_product";
        className2 = "updateRequisitionProduct";
      } else {
        id = data.id_requisition_material;
        className1 = "id_requisition_material";
        className2 = "updateRequisitionMaterial";
      }

      action = `<div style="display: flex; align-items: center; justify-content: center; height: 100%;">
                  <span id="upd-${id}" class="badge badge-warning ${className2} btn-action"
                        style="cursor: pointer; font-size: 12px; margin-right: 5px;" 
                        data-toggle='tooltip' title='Ejecutar Requisición' onclick="executeRequisition('${id}')">
                    Ejecutar
                  </span>
                  <a href="javascript:;" <i id="${id}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Requisicion' style="font-size: 30px;color:red" onclick="deleteFunction(${
        visible == true ? "2" : "1"
      }, ${className1})"></i></a>
                </div>`;
    } else if (data.status == "Proceso") {
      action = `<a href="javascript:;" <i id="upd-${id}" class="bx bx-edit-alt ${className2}" data-toggle='tooltip' title='Editar Requisicion' style="font-size: 30px;"></i></a>
              <a href="javascript:;" <i id="${id}" class="bi bi-x-circle-fill" data-toggle='tooltip' title='Cancelar Requisicion' style="font-size: 30px;color:red" onclick="cancelRQFunction(${className1})"></i></a>`;
    } else {
      action = data.admission_date;
    }
    return action;
  }

  const pendingTab = document.getElementById("pending");
  const doneTab = document.getElementById("done");
  const pendingIcon = document.getElementById("pending-icon");
  const doneIcon = document.getElementById("done-icon");


  pendingTab.addEventListener("click", () => {
    pendingIcon.style.color = "red";
    doneIcon.style.color = "gray";
  });

  doneTab.addEventListener("click", () => {
    // Cambiar color del icono de pendiente a transparente y el de ejecutado a verde
    pendingIcon.style.color = "gray";
    doneIcon.style.color = "green";
  });


});
