$(document).ready(function () {
  /* Cargue tabla de Materias Primas */
  flag_products_measure == "1" ? (visible = true) : (visible = false);

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
          title: "Tipo",
          data: "material_type",
          className: "uniqueClassName dt-head-center",
          visible: visible,
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
          title: "Gramaje",
          data: null,
          className: "uniqueClassName dt-head-center",
          visible: visible,
          render: function (data) {
            let grammage = parseFloat(data.grammage);

            !grammage ? (grammage = 0) : grammage;

            return grammage.toLocaleString("es-CO", {
              maximumFractionDigits: 2,
            });
          },
        },
        {
          title: "Medida",
          data: "abbreviation",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Existencias",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: function (data) {
            data.unit == "UNIDAD"
              ? (number = parseFloat(data.quantity).toLocaleString("es-CO", {
                  maximumFractionDigits: 0,
                }))
              : (number = parseFloat(data.quantity).toLocaleString("es-CO", {
                  minimumFractionDigits: 2,
                }));
            return number;
          },
        },
        {
          title: "Transito",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: function (data) {
            data.unit == "UNIDAD"
              ? (number = parseFloat(data.transit).toLocaleString("es-CO", {
                  maximumFractionDigits: 0,
                }))
              : (number = parseFloat(data.transit).toLocaleString("es-CO", {
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
              ? (number = parseFloat(data.reserved).toLocaleString("es-CO", {
                  maximumFractionDigits: 0,
                }))
              : (number = parseFloat(data.reserved).toLocaleString("es-CO", {
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
              ? (number = parseFloat(data.minimum_stock).toLocaleString(
                  "es-CO",
                  {
                    maximumFractionDigits: 0,
                  }
                ))
              : (number = parseFloat(data.minimum_stock).toLocaleString(
                  "es-CO",
                  {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                  }
                ));
            return number;
          },
        },
        {
          title: "Dias Inv",
          data: "days",
          className: "uniqueClassName dt-head-center",
          render: (data) =>
            parseFloat(data).toLocaleString("es-CO", {
              minimumFractionDigits: 0,
              maximumFractionDigits: 0,
            }),
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
      footerCallback: function (row, data, start, end, display) {
        let quantity = 0;
        let grammage = 0;
        let transit = 0;
        let reserved = 0;
        let minimum_stock = 0;
        let days = 0;

        for (i = 0; i < display.length; i++) {
          quantity += parseFloat(data[display[i]].quantity);
          grammage += parseFloat(data[display[i]].grammage);
          transit += parseFloat(data[display[i]].transit);
          reserved += parseFloat(data[display[i]].reserved);
          minimum_stock += parseFloat(data[display[i]].minimum_stock);
          days += parseFloat(data[display[i]].days);
        }

        !grammage ? (grammage = 0) : grammage;

        $("#totalQuantity").html(
          quantity.toLocaleString("es-CO", {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
          })
        );
        $("#totalGrammage").html(
          grammage.toLocaleString("es-CO", {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
          })
        );

        $("#totalTransit").html(
          transit.toLocaleString("es-CO", {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
          })
        );

        $("#totalReserved").html(
          reserved.toLocaleString("es-CO", {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
          })
        );

        $("#totalStock").html(
          minimum_stock.toLocaleString("es-CO", {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
          })
        );

        $("#totalDay").html(
          days.toLocaleString("es-CO", {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
          })
        );
      },
    });
  };
});
