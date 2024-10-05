$(document).ready(function () {
  /* Cargue tabla de Máquinas */
  loadTblProductType = (data) => {
    tblProductsType = $("#tblProductsType").dataTable({
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
          className: "uniqueClassName dt-head-center ",
          render: function (data, type, full, meta) {
            return meta.row + 1;
          },
        },
        {
          title: "Tipo Producto",
          data: "product_type",
          className: "uniqueClassName dt-head-center ",
        },
        {
          title: "Acciones",
          data: "id_product_type",
          className: "uniqueClassName dt-head-center ",
          render: function (data) {
            return `
                <a href="javascript:;" <i id="upd-${data}" class="bx bx-edit-alt updateProductType" data-toggle='tooltip' title='Actualizar Tipo' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Tipo' style="font-size: 30px;color:red" onclick="deletePTFunction()"></i></a>`;
          },
        },
      ],
    });
  };
});
