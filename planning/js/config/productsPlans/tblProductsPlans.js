$(document).ready(function () {
  loadTblProductPlans = (idProduct) => {
    tblPlans = $("#tblPlans").dataTable({
      destroy: true,
      pageLength: 50,
      ajax: {
        url: `/api/productsPlans/${idProduct}`,
        dataSrc: "",
      },
      language: {
        url: "/assets/plugins/i18n/Spanish.json",
      },
      columns: [
        {
          title: "Acciones",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: (data) => ` 
                        <a href="javascript:;">
                            <i id="upd-${data.id_product_plan}" class="bx bx-edit-alt updateProductPlan" data-toggle='tooltip' title='Actualizar Planos' style="font-size: 30px;"></i>
                        </a>
                        <a href="javascript:;">
                            <i id="${data.id_product_plan}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Planos' style="font-size: 30px;color:red" onclick="deleteProductsPlanFunction()"></i>
                        </a>
                    `,
        },
        {
          title: "No.",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: function (data, type, full, meta) {
            return meta.row + 1;
          },
        },
        {
          title: "Plano Mecanico",
          data: "mechanical_plan",
          className: "uniqueClassName dt-head-center",
          render: (data) =>
            data
              ? `<a href="javascript:;">
                                    <i id="mechanical_plan" class="bi bi-file-earmark-pdf-fill downloadPlaneProduct" data-toggle='tooltip' title='Descargar Plano Mecanico' style="font-size: 30px;color:red"></i>
                                </a>`
              : "",
        },
        {
          title: "Plano Montaje",
          data: "assembly_plan",
          className: "uniqueClassName dt-head-center",
          render: (data) =>
            data
              ? `<a href="javascript:;">
                                    <i id="assembly_plan" class="bi bi-file-earmark-pdf-fill downloadPlaneProduct" data-toggle='tooltip' title='Descargar Plano Montaje' style="font-size: 30px;color:red"></i>
                                </a>`
              : "",
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
    });
  };
});
