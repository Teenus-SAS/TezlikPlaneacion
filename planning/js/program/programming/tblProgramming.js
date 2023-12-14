/* Función mover filas

let shadow;
function dragit(event) {
  shadow = event.target;
}
function dragover(e) {
  let children = Array.from(e.target.parentNode.parentNode.children);
  if (children.indexOf(e.target.parentNode) > children.indexOf(shadow))
    e.target.parentNode.after(shadow);
  else e.target.parentNode.before(shadow);
}*/
$(document).ready(function () {
  sessionStorage.removeItem("id_programming");

  $("#searchMachine").change(function (e) {
    e.preventDefault();

    loadTblProgramming(this.value);
  });

  loadTblProgramming = async (machine) => {
    let data;

    if (machine == 0) {
      data = await searchData("/api/programming");
    } else data = await searchData(`/api/programmingByMachine/${machine}/0`);

    if ($.fn.dataTable.isDataTable("#tblProgramming")) {
      $("#tblProgramming").DataTable().clear();
      $("#tblProgramming").DataTable().rows.add(data).draw();
      return;
    }

    tblProgramming = $("#tblProgramming").dataTable({
      // destroy: true,
      pageLength: 50,
      data: data,
      language: {
        url: "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json",
      },
      dom: "Bfrtip",
      buttons: [
        {
          extend: "excel",
          exportOptions: {
            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
          },
        },
      ],
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
          title: "Pedido",
          data: "num_order",
          className: "uniqueClassName",
        },
        {
          title: "Referencia",
          data: "reference",
          className: "uniqueClassName",
        },
        {
          title: "Producto",
          data: "product",
          className: "uniqueClassName",
        },
        {
          title: "Maquina",
          data: "machine",
          className: "uniqueClassName",
        },
        {
          title: "Cantidades",
          data: null,
          className: "uniqueClassName",
          render: function (data, type, full, meta) {
            const quantityOrder = full.quantity_order;
            const quantityProgramming = full.quantity_programming;
            const accumulatedQuantity = full.accumulated_quantity;

            return `Pedido: ${quantityOrder}<br>Fabricar: ${quantityProgramming}<br>Pendiente: ${accumulatedQuantity}`;
          },
        },
        {
          title: "Cliente",
          data: "client",
          className: "uniqueClassName",
        },

        {
          title: "Fechas",
          data: null,
          className: "uniqueClassName",
          render: function (data, type, full, meta) {
            const minDate = full.min_date;
            const maxDate = full.max_date;

            return `Inicio: ${moment(minDate).format(
              "DD/MM/YYYY"
            )}<br>Fin: ${moment(maxDate).format("DD/MM/YYYY")}`;
          },
        },
        {
          title: "Orden Produccion",
          data: "id_programming",
          className: "uniqueClassName",
          render: function (data) {
            return `
            <button class="btn btn-warning changeStatus " id="${data}" name="${data}">Crear OP</button>`;
          },
        },
        {
          title: "Acciones",
          data: "id_programming",
          className: "uniqueClassName",
          render: function (data) {
            return `
                <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateProgramming" data-toggle='tooltip' title='Actualizar Programa' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Programa' style="font-size: 30px;color:red" onclick="deleteFunction()"></i></a>`;
          },
        },
      ],
    });
  };

  loadTblProgramming(0);
});
