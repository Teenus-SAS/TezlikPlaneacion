$(document).ready(function () {
  $(".selectNavigation").click(function (e) {
    e.preventDefault();

    if (this.id == "deliver") {
      loadTblOffices(pendingStore, true);
      officesIndicators(pendingStore);
    }
    else if (this.id == "delivered") {
      loadTblOffices(deliveredStore, false);
      officesIndicators(deliveredStore);
    }
  });

  loadAllData = async (op, min_date, max_date) => {
    try {
      const [dataActualOffices, dataOffices] = await Promise.all([
        searchData("/api/actualOffices"),
        op == 3 ? searchData(`/api/offices/${min_date}/${max_date}`) : null,
      ]);

      let card = document.getElementsByClassName("selectNavigation");

      if (card[0].className.includes("active")) pending = 1;
      else pending = 0;

      pendingStore = dataActualOffices.filter(
        (item) => item.status !== "ENTREGADO"
      );
      deliveredStore = dataActualOffices.filter(
        (item) => item.status === "ENTREGADO"
      );

      let visible = true;
      if (op === 1) dataToLoad = pendingStore;
      else if (op === 2) {
        dataToLoad = deliveredStore;
        visible = false;
      } else {
        if (pending == 1)
          dataToLoad = dataOffices.filter(
            (item) => item.status !== "ENTREGADO"
          );
        else
          dataToLoad = dataOffices.filter(
            (item) => item.status === "ENTREGADO"
          );
      }

      if (dataToLoad) {
        loadTblOffices(dataToLoad, visible);
        officesIndicators(dataToLoad);
      }
    } catch (error) {
      console.error("Error loading data:", error);
    }
  };

  const officesIndicators = (data) => {
    let totalQuantity = 0;
    let completed = 0;
    let late = 0;
    let today = formatDate(new Date());

    if (data.length > 0) {
      let arrCompleted = data.filter(item => item.max_date > today);
      let arrLate = data.filter(item => item.max_date < today);

      totalQuantity = data.length;
      completed = (arrCompleted.length / totalQuantity) * 100;
      late = (arrLate.length / totalQuantity) * 100;
    }

    $('#lblTotal').html(` Total: ${totalQuantity.toLocaleString('es-CO', { maximumFractionDigits: 0 })}`);
    $('#lblCompleted').html(` A Tiempo: ${completed.toLocaleString('es-CO', { maximumFractionDigits: 2 })} %`);
    $('#lblLate').html(` Atrasados: ${late.toLocaleString('es-CO', { maximumFractionDigits: 2 })} %`);
  }

  /* Cargar pedidos */
  loadTblOffices = (data, visible) => {
    // if ($.fn.dataTable.isDataTable("#tblOffices")) {
    //   $("#tblOffices").DataTable().clear();
    //   $('#tblOffices').DataTable().column(8).visible(visible);
    //   $('#tblOffices').DataTable().column(10).visible(visible);
    //   $("#tblOffices").DataTable().rows.add(data).draw();
    //   return;
    // }

    tblOffices = $("#tblOffices").dataTable({
      destroy: true,
      pageLength: 50,
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
          title: "Fecha",
          data: "date_order",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Pedido",
          data: "num_order",
          className: "uniqueClassName dt-head-center",
        },

        {
          title: "Cliente",
          data: "client",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Fechas",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: function (data, type, full, meta) {
            const minDate = full.min_date;
            const maxDate = full.max_date;
            // Convierte la fecha mínima a un objeto Date
            const parsedMaxDate = new Date(maxDate);

            // Obtiene la fecha del sistema
            const today = new Date();

            // Compara las fechas
            const isOverdue = parsedMaxDate > today;
            const equal = parsedMaxDate == today;

            if (isOverdue)
              return `<span class="badge badge-info">Mínima: ${moment(minDate).format("DD/MM/YYYY")}</span><br><span class="badge badge-success">Máxima: ${moment(maxDate).format("DD/MM/YYYY")}</span>`;
            else if (equal)
              return `<span class="badge badge-info">Mínima: ${moment(minDate).format("DD/MM/YYYY")}</span><br><span class="badge badge-warning">Máxima: ${moment(maxDate).format("DD/MM/YYYY")}</span>`;
            else
              return `<span class="badge badge-info">Mínima: ${moment(minDate).format("DD/MM/YYYY")}</span><br><span class="badge badge-danger">Máxima: ${moment(maxDate).format("DD/MM/YYYY")}</span>`;
          },
        },
        {
          title: "Ref",
          data: "reference",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Producto",
          data: "product",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Cantidad",
          data: "original_quantity",
          className: "uniqueClassName dt-head-center",
          render: $.fn.dataTable.render.number(".", ",", 0, ""),
        },
        {
          title: "Existencias",
          visible: visible,
          data: "quantity",
          className: "uniqueClassName dt-head-center",
          render: $.fn.dataTable.render.number(".", ",", 0),
        }, 
        {
          title: "F.Entrega",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: function (data) {
            data.status == "DESPACHO"
              ? (action = `<button class="btn btn-info changeDate" id="${data.id_order}" name="${data.id_order}">Entregar</button>`)
              : (action = `${data.firstname_order} ${data.lastname_order}<br>${data.delivery_date}`);

            return action;
          },
        },
        {
          title: "Cancelar",
          data: null,
          className: "uniqueClassName dt-head-center",
          visible: visible,
          render: function (data) {
            return data.status == "DESPACHO"
              ? `<a href="javascript:;" <i class="fas fa-times cancelOrder" id="${data.id_order}" data-toggle='tooltip' title='Cancelar DESPACHO' style="font-size: 30px;color:red;"></i></a>`
              : "";
          },
        },
      ],
    });
  };

  loadAllData(1, null, null);
});
