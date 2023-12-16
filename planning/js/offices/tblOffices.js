$(document).ready(function () {
  $('.selectNavigation').click(function (e) {
    e.preventDefault();
    
    if (this.id == 'deliver')
      loadTblOffices(pendingStore, true);
    else if (this.id == 'delivered')
      loadTblOffices(deliveredStore, false);
  });

  loadAllData = async (op, min_date, max_date) => {
    try {
      const [dataActualOffices, dataOffices] = await Promise.all([
        searchData('/api/actualOffices'),
        op == 3 ? searchData(`/api/offices/${min_date}/${max_date}`) : null
      ]);

      let card = document.getElementsByClassName('selectNavigation');

      if (card[0].className.includes('active'))
        pending = 1;
      else
        pending = 0;

      pendingStore = dataActualOffices.filter(item => item.status !== 'Entregado');
      deliveredStore = dataActualOffices.filter(item => item.status === 'Entregado');
      
      let visible = true;
      if (op === 1)
        dataToLoad = pendingStore;
      else if (op === 2) {
        dataToLoad = deliveredStore
        visible = false;
      } else {
        if (pending == 1)
          dataToLoad = dataOffices.filter(item => item.status !== 'Entregado');
        else
          dataToLoad = dataOffices.filter(item => item.status === 'Entregado');
      }

      if (dataToLoad) {
        loadTblOffices(dataToLoad, visible);
      }
    } catch (error) {
      console.error('Error loading data:', error);
    }
  };

  /* Cargar pedidos */
  loadTblOffices = (data, visible) => {
    if ($.fn.dataTable.isDataTable("#tblOffices")) {
      $("#tblOffices").DataTable().clear();
      $("#tblOffices").DataTable().rows.add(data).draw();
      return;
    }

    tblOffices = $("#tblOffices").dataTable({
      // destroy: true,
      pageLength: 50,
      data: data,
      language: {
        url: "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json",
      },
      columns: [
        {
          title: "No.",
          data: null,
          className: "uniqueClassName",
          render: function (data, type, full, meta) {
            return meta.row + 1;
          },
        },
        {
          title: "Fecha",
          data: "date_order",
          className: "uniqueClassName",
        },
        {
          title: "Pedido",
          data: "num_order",
          className: "uniqueClassName",
        },

        {
          title: "Cliente",
          data: "client",
          className: "uniqueClassName",
        },
        {
          title: "Fechas",
          data: null,
          className: "classCenter",
          render: function (data, type, full, meta) {
            const minDate = full.min_date;
            const maxDate = full.max_date;
            return `Mínima: ${moment(minDate).format(
              "DD/MM/YYYY"
            )}<br>Máxima: ${moment(maxDate).format("DD/MM/YYYY")}`;
          },
        },
        {
          title: "Ref",
          data: "reference",
          className: "uniqueClassName",
        },
        {
          title: "Producto",
          data: "product",
          className: "uniqueClassName",
        },
        {
          title: "Cantidad",
          data: "original_quantity",
          className: "classCenter",
          render: $.fn.dataTable.render.number(".", ",", 0, ""),
        },
        {
          title: "Existencias",
          data: "quantity",
          className: "classCenter",
          render: $.fn.dataTable.render.number(".", ",", 0),
        },
        {
          title: "Estado",
          data: "status",
          className: "classCenter",
          render: function (data) {
            if (data == "Entregado") badge = "badge-success";
            else if (data == "Sin Ficha Tecnica" || data == "Sin Materia Prima")
              badge = "badge-danger";
            else if (data == "Despacho") badge = "badge-info";
            else badge = "badge-light";

            return `<span class="badge ${badge}">${data}</span>`;
          },
        },
        {
          title: "F.Entrega",
          data: null,
          className: "classCenter",
          render: function (data) {
            data.status == "Despacho"
              ? (action = `<button class="btn btn-warning changeDate" id="${data.id_order}" name="${data.id_order}">Entregar</button>`)
              : (action = data.delivery_date);

            return action;
          },
        },
        {
          title: "Cancelar",
          data: null,
          className: "classCenter",
          visible: visible,
          render: function (data) {
            return data.status == "Despacho"
              ? `<a href="javascript:;" <i class="bi bi-x-octagon-fill cancelOrder" id="${data.id_order}" data-toggle='tooltip' title='Cancelar Despacho' style="font-size: 30px;color:red;"></i></a>`
              : "";
          },
        },
      ],
    });
  };

  loadAllData(1, null, null);
});
