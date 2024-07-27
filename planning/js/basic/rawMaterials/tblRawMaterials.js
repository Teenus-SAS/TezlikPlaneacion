$(document).ready(function () {
  /* Cargue tabla de Materias Primas */

  loadTblMaterials = (data) => {
    if ($.fn.dataTable.isDataTable("#tblRawMaterials")) {
      $("#tblRawMaterials").DataTable().clear();
      $("#tblRawMaterials").DataTable().rows.add(data).draw();
      return;
    }
    
    tblRawMaterials = $("#tblRawMaterials").dataTable({
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
          title: "Medida",
          data: "abbreviation",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Existencia",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: function (data) {
            data.unit == "UNIDAD"
              ? (number = data.quantity.toLocaleString("es-CO", {
                maximumFractionDigits: 0,
              }))
              : (number = data.quantity.toLocaleString("es-CO", {
                minimumFractionDigits: 2,
              }));
            return number;
          },
        },
        {
          title: "Reservado",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: function (data) {
            data.unit == "UNIDAD"
              ? (number = data.reserved.toLocaleString("es-CO", {
                maximumFractionDigits: 0,
              }))
              : (number = data.reserved.toLocaleString("es-CO", {
                minimumFractionDigits: 2,
              }));
            return number;
          },
        },
        {
          title: "Stock Min",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: function (data) {
            data.unit == "UNIDAD"
              ? (number = parseFloat(data.minimum_stock).toLocaleString("es-CO", {
                maximumFractionDigits: 0,
              }))
              : (number = parseFloat(data.minimum_stock).toLocaleString("es-CO", {
                minimumFractionDigits: 2, maximumFractionDigits:2,
              }));
            return number;
          },
        },
        {
          title: "Acciones",
          data: "id_material",
          className: "uniqueClassName dt-head-center",
          render: function (data) {
            return `
                <a href="javascript:;" <i id="upd-${data}" class="bx bx-edit-alt updateRawMaterials" data-toggle='tooltip' title='Actualizar Materia Prima' style="font-size: 30px;"></i></a>
                <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Materia Prima' style="font-size: 30px;color:red" onclick="deleteMaterialsFunction()"></i></a>`;
          },
        },
      ],
      footerCallback: function (row, data, start, end, display) {
        let quantity = 0;
        let reserved = 0;
        let minimum_stock = 0;

        for (i = 0; i < display.length; i++) {
          quantity += parseFloat(data[display[i]].quantity);
          reserved += parseFloat(data[display[i]].reserved);
          minimum_stock += parseFloat(data[display[i]].minimum_stock);
        }

        $(this.api().column(4).footer()).html(
          quantity.toLocaleString('es-CO', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
          })
        );
        
        $(this.api().column(5).footer()).html(
          reserved.toLocaleString('es-CO', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
          })
        );

        $(this.api().column(6).footer()).html(
          minimum_stock.toLocaleString('es-CO', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
          })
        );
      },
    });
  }
});
