$(document).ready(function () {
  /* Seleccion producto */

  $("#refProduct").change(function (e) {
    e.preventDefault();
    id = this.value;
    $("#selectNameProduct option").removeAttr("selected");
    $(`#selectNameProduct option[value=${id}]`).prop("selected", true);
    $('.cardAddMaterials').hide(800);
    loadtableMaterials(id);
    loadTblPlanCiclesMachine(id);
    loadTblRoutes(id);
  });

  $("#selectNameProduct").change(function (e) {
    e.preventDefault();
    id = this.value;
    $("#refProduct option").removeAttr("selected");
    $(`#refProduct option[value=${id}]`).prop("selected", true);
    $('.cardAddMaterials').hide(800);
    loadtableMaterials(id);
    loadTblPlanCiclesMachine(id);
    loadTblRoutes(id);
  });

  /* Cargue tabla de Productos Materiales */

  loadtableMaterials = (idProduct) => {
    tblConfigMaterials = $("#tblConfigMaterials").dataTable({
      destroy: true,
      pageLength: 50,
      ajax: {
        url: `/api/productsMaterials/${idProduct}`,
        dataSrc: "",
      },
      dom: '<"datatable-error-console">frtip',
      language: {
        url: "/assets/plugins/i18n/Spanish.json",
      },
      fnInfoCallback: function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
        if (oSettings.json && oSettings.json.hasOwnProperty("error")) {
          console.error(oSettings.json.error);
        }
      },
      columnDefs: [
        {
          //targets: 0,
          // Centra los títulos del header
          className: "",
        },
      ],
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
          title: "Referencia",
          data: "reference",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Materia Prima",
          data: "material",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Unidad",
          data: "abbreviation",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Cantidad",
          data: "quantity",
          className: "uniqueClassName dt-head-center",
          render: $.fn.dataTable.render.number(".", ",", 4, ""),
        },
        {
          title: "Acciones",
          data: "id_product_material",
          className: "uniqueClassName dt-head-center",
          render: function (data) {
            return `
                <a href="javascript:;" <i id="${data}" class="bx bx-edit-alt updateMaterials" data-toggle='tooltip' title='Actualizar Materia Prima' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Materia Prima' style="font-size: 30px;color:red" onclick="deleteMaterial()"></i></a>`;
          },
        },
      ],
      footerCallback: function (row, data, start, end, display) {
        let quantity = 0;  

        for (i = 0; i < display.length; i++) {
          quantity += parseFloat(data[display[i]].quantity);  
        }

        $(this.api().column(4).footer()).html(
          quantity.toLocaleString('es-CO', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
          })
        );  
      },
    });
  };
  /* Cargue tabla de Productos en proceso 

  const loadTableProcess = (idProduct) => {
    $('.cardTableProductsInProcess').show(800);

    tblProductsInProcess = $('#tblProductsInProcess').dataTable({
      destroy: true,
      pageLength: 50,
      ajax: {
        url: `/api/productsInProcessByCompany/${idProduct}`,
        dataSrc: '',
      },
      language: {
        url: '/assets/plugins/i18n/Spanish.json',
      },
      columns: [
        {
          title: 'No.',
          data: null,
          className: 'uniqueClassName dt-head-center',
          render: function (data, type, full, meta) {
            return meta.row + 1;
          },
        },
        {
          title: 'Referencia',
          data: 'reference',
          className: 'uniqueClassName dt-head-center',
        },
        {
          title: 'Producto',
          data: 'product',
          className: 'uniqueClassName dt-head-center',
        },
        {
          title: 'Acciones',
          data: 'id_product_category',
          className: 'uniqueClassName dt-head-center',
          render: function (data) {
            return `
              <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Producto' style="font-size: 30px;color:red" onclick="deleteProduct()"></i></a>`;
          },
        },
      ],
    });
  }; */
});
