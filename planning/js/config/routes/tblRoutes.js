$(document).ready(function () {
  // Mostrar Tabla planeacion maquinas
  loadTblRoutes = (idProduct) => {
    tblRoutes = $("#tblRoutes").dataTable({
      destroy: true,
      pageLength: 50,
      ajax: {
        url: `/api/routesCiclesMachine/${idProduct}`,
        dataSrc: "",
      },
      language: {
        url: "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json",
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
            title: 'Proceso',
            data: 'process',
            className: 'uniqueClassName dt-head-center',
        },
        {
          title: "Máquina",
          data: "machine",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "",
          data: "id_cicles_machine",
          className: "text-center",
        },
      ],
      drawCallback: function (settings) {
        const recordsTotal = tblRoutes.fnSettings().fnRecordsTotal();
        const recordsDisplay = tblRoutes.fnSettings().fnRecordsDisplay();

        $("#tblRoutes tbody tr").each(function (index) {
          const moveUpBtn =
            index > 0
              ? `<a href="javascript:;" data-index="${index}"><i class="${index} bi bi-arrow-up-circle-fill move mt-1 ml-1 up" style="color: steelblue;"></i></a>`
              : "";

          const moveDownBtn =
            index < recordsDisplay - 1 && index < recordsTotal - 1
              ? `<a href="javascript:;" data-index="${index}"><i class="${index} bi bi-arrow-down-circle-fill move mt-1 ml-1 down" style="color: steelblue;"></i></a>`
              : "";

          $(this).find("td:last-child").html(`<div class="btn-group" id="actionRoute-${index}" role="group" style="color: blue;font-size:30px">${moveUpBtn}${moveDownBtn}</div>`);
        });
      },
    });
  };
});
