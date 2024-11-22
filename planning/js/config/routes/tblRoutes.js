$(document).ready(function () {
  // Mostrar Tabla planeacion maquinas
  loadTblRoutes = (idProduct) => {
    tblRoutes = $("#tblRoutes").dataTable({
      destroy: true,
      autoWidth: false,
      pageLength: 50,
      ajax: {
        url: `/api/routesCiclesMachine/${idProduct}`,
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
          render: (data, type, full, meta) => meta.row + 1,
        },
        {
          title: "Proceso",
          data: "process",
          className: "uniqueClassName dt-head-center",
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
      headerCallback: (thead) => {
        $(thead)
          .find("th")
          .css({
            "background-color": "#386297",
            color: "white",
            "text-align": "center",
            "font-weight": "bold",
            padding: "10px",
            border: "1px solid #ddd",
          });
      },
      drawCallback: () => {
        const recordsTotal = tblRoutes.fnSettings().fnRecordsTotal();
        const recordsDisplay = tblRoutes.fnSettings().fnRecordsDisplay();

        $("#tblRoutes tbody tr").each((index) => {
          const moveUpBtn = index > 0
            ? `<a href="javascript:;" data-index="${index}">
                <i class="${index} bi bi-arrow-up-circle-fill move mt-1 ml-1 up" style="color: steelblue;"></i>
             </a>`
            : "";

          const moveDownBtn = (index < recordsDisplay - 1 && index < recordsTotal - 1)
            ? `<a href="javascript:;" data-index="${index}">
                <i class="${index} bi bi-arrow-down-circle-fill move mt-1 ml-1 down" style="color: steelblue;"></i>
             </a>`
            : "";

          $(this).find("td:last-child").html(`
          <div class="btn-group" id="actionRoute-${index}" role="group" style="color: blue;font-size:30px">
            ${moveUpBtn}${moveDownBtn}
          </div>
        `);
        });
      },
    });
  };
});
