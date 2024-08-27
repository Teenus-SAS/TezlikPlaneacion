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
            <i id="upd-${data.id_product_measure}" class="bx bx-edit-alt updatePMeasure" data-toggle='tooltip' title='Actualizar Medida' style="font-size: 30px;"></i>
          </a>
          <a href="javascript:;">
            <i id="${data.id_product_measure}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Medida' style="font-size: 30px;color:red" onclick="deletePMeasureFunction()"></i>
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
                //   {
                //     title: "Referencia",
                //     data: "reference",
                //     className: "uniqueClassName dt-head-center",
                //   },
                //   {
                //     title: "Producto",
                //     data: "product",
                //     className: "uniqueClassName dt-head-center",
                //   },
                {
                    title: "Plano Mecanico",
                    data: "mechanical_plan",
                    className: "uniqueClassName dt-head-center",
                    render: (data) =>
                        data
                            ? `<img src="${data}" alt="" style="width:80px;border-radius:100px">`
                            : "",
                },
                {
                    title: "Plano Montaje",
                    data: "assembly_plan",
                    className: "uniqueClassName dt-head-center",
                    render: (data) =>
                        data
                            ? `<img src="${data}" alt="" style="width:80px;border-radius:100px">`
                            : "",
                },
            ],
        });
    }
});