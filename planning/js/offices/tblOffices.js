$(document).ready(function () {
  $(".selectNavigation").click(function (e) {
    e.preventDefault();

    if (this.id == "deliver") {
      loadTblOffices(pendingStore, true);
      officesIndicators(pendingStore);
    } else if (this.id == "delivered") {
      loadTblOffices(deliveredStore, false);
      officesIndicators(deliveredStore);
    }
  });

  // loadAllData = async (op, min_date, max_date) => {
  //   try {
  //     const [dataActualOffices, dataOffices] = await Promise.all([
  //       searchData("/api/actualOffices"),
  //       op == 3 ? searchData(`/api/offices/${min_date}/${max_date}`) : null,
  //     ]);

  //     let card = document.getElementsByClassName("selectNavigation");

  //     if (card[0].className.includes("active")) pending = 1;
  //     else pending = 0;

  //     pendingStore = dataActualOffices.filter(
  //       (item) => item.status !== "ENTREGADO"
  //     );
  //     deliveredStore = dataActualOffices.filter(
  //       (item) => item.status === "ENTREGADO"
  //     );

  //     let visible = true;
  //     if (op === 1) dataToLoad = pendingStore;
  //     else if (op === 2) {
  //       dataToLoad = deliveredStore;
  //       visible = false;
  //     } else {
  //       if (pending == 1)
  //         dataToLoad = dataOffices.filter(
  //           (item) => item.status !== "ENTREGADO"
  //         );
  //       else
  //         dataToLoad = dataOffices.filter(
  //           (item) => item.status === "ENTREGADO"
  //         );
  //     }

  //     if (dataToLoad) {
  //       loadTblOffices(dataToLoad, visible);
  //       officesIndicators(dataToLoad);
  //     }
  //   } catch (error) {
  //     console.error("Error loading data:", error);
  //   }
  // };
  loadAllData = async (op, min_date, max_date) => {
    try {
      // Cargar los datos de forma condicional con Promise.all
      const [dataActualOffices, dataOffices] = await Promise.all([
        searchData("/api/actualOffices"),
        op === 3
          ? searchData(`/api/offices/${min_date}/${max_date}`)
          : Promise.resolve(null),
      ]);

      // Detectar si la pestaña "pendientes" está activa
      const isPendingActive = document
        .getElementsByClassName("selectNavigation")[0]
        .className.includes("active");
      const pending = isPendingActive ? 1 : 0;

      // Filtrar datos según el estado
      pendingStore = dataActualOffices.filter(
        (item) => item.status !== "ENTREGADO"
      );
      deliveredStore = dataActualOffices.filter(
        (item) => item.status === "ENTREGADO"
      );

      // Definir los datos a cargar y la visibilidad de la columna
      let dataToLoad = [];
      let visible = true;

      if (op === 1) {
        // Cargar datos pendientes
        dataToLoad = pendingStore;
      } else if (op === 2) {
        // Cargar datos entregados
        dataToLoad = deliveredStore;
        visible = false;
      } else if (op === 3 && dataOffices) {
        // Filtrar los datos según el estado y la pestaña activa
        dataToLoad = pending
          ? dataOffices.filter((item) => item.status !== "ENTREGADO")
          : dataOffices.filter((item) => item.status === "ENTREGADO");
      }

      // Si hay datos para cargar, actualizar la tabla y los indicadores
      if (dataToLoad && dataToLoad.length > 0) {
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
      let arrCompleted = data.filter((item) => item.max_date > today);
      let arrLate = data.filter((item) => item.max_date < today);

      totalQuantity = data.length;
      completed = (arrCompleted.length / totalQuantity) * 100;
      late = (arrLate.length / totalQuantity) * 100;
    }

    $("#lblTotal").html(
      ` Total: ${totalQuantity.toLocaleString("es-CO", {
        maximumFractionDigits: 0,
      })}`
    );
    $("#lblCompleted").html(
      ` A Tiempo: ${completed.toLocaleString("es-CO", {
        maximumFractionDigits: 2,
      })} %`
    );
    $("#lblLate").html(
      ` Atrasados: ${late.toLocaleString("es-CO", {
        maximumFractionDigits: 2,
      })} %`
    );
  };

  // Cargar Despachos
  const loadTblOffices = (data, visible) => {
    if ($.fn.dataTable.isDataTable("#tblOffices")) {
      // Si ya existe, solo actualizamos los datos y columnas visibles
      $("#tblOffices").DataTable().clear();
      $("#tblOffices").DataTable().column(8).visible(visible); // Columna "Existencias"
      $("#tblOffices").DataTable().rows.add(data).draw();
      return;
    }

    // Inicializar tabla si no existe
    tblOffices = $("#tblOffices").dataTable({
      fixedHeader: true,
      scrollY: "400px",
      scrollCollapse: true,
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
          render: function (data, type, full) {
            const minDate = moment(full.min_date).format("DD/MM/YYYY");
            const maxDate = moment(full.max_date).format("DD/MM/YYYY");
            const today = moment().format("YYYY-MM-DD");

            let badgeClass = "badge-success"; // Por defecto
            if (full.max_date < today) badgeClass = "badge-danger"; // Vencido
            else if (full.max_date === today) badgeClass = "badge-warning"; // Hoy

            return `
            <span class="badge badge-info">Mínima: ${minDate}</span><br>
            <span class="badge ${badgeClass}">Máxima: ${maxDate}</span>
          `;
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
            return data.status === "DESPACHO"
              ? `<button class="btn btn-info changeDate" id="${data.id_order}" name="${data.id_order}">Entregar</button>`
              : `${data.firstname_order} ${data.lastname_order}<br>${data.delivery_date}`;
          },
        },
      ],
      headerCallback: function (thead) {
        $(thead).find("th").css({
          "background-color": "#386297",
          color: "white",
          "text-align": "center",
          "font-weight": "bold",
          padding: "10px",
          border: "1px solid #ddd",
        });
      },
    });
  };

  loadAllData(1, null, null);
});
