$(document).ready(function () {
  /* Seleccion producto */

  $("#refProduct").change(function (e) {
    e.preventDefault();
    let id = this.value;
    var composite = parseInt($(this).find("option:selected").attr("class"));
    let visible;

    composite == 1 ? visible = false : visible = true;

    $("#selectNameProduct option").removeAttr("selected");
    $(`#selectNameProduct option[value=${id}]`).prop("selected", true);

    let dataProducts = JSON.parse(sessionStorage.getItem("dataProducts"));
    let compositeProduct = dataProducts.filter(
      (item) => item.composite == 1 && item.id_product != this.value
    );

    populateOptions("#refCompositeProduct", compositeProduct, "reference");
    populateOptions("#compositeProduct", compositeProduct, "product");

    $(".cardAddMaterials").hide(800);
    loadAllDataMaterials(id, visible);
    loadTblPlanCiclesMachine(id, visible);
    loadTblRoutes(id);
    loadTblProductPlans(id);
  });

  $("#selectNameProduct").change(function (e) {
    e.preventDefault();
    let id = this.value;
    var composite = parseInt($(this).find("option:selected").attr("class"));
    let visible;

    composite == 1 ? visible = false : visible = true;

    $("#refProduct option").removeAttr("selected");
    $(`#refProduct option[value=${id}]`).prop("selected", true);

    let dataProducts = JSON.parse(sessionStorage.getItem("dataProducts"));
    let compositeProduct = dataProducts.filter(
      (item) => item.composite == 1 && item.id_product != this.value
    );

    populateOptions("#refCompositeProduct", compositeProduct, "reference");
    populateOptions("#compositeProduct", compositeProduct, "product");

    $(".cardAddMaterials").hide(800);
    loadAllDataMaterials(id, visible);
    loadTblPlanCiclesMachine(id, visible);
    loadTblRoutes(id);
    loadTblProductPlans(id);
  });

  /* Cargue tabla de Productos Materiales */
  loadAllDataMaterials = async (id, visible) => {
    try {
      const [dataProductMaterials, dataCompositeProduct] = await Promise.all([
        searchData(`/api/productsMaterials/${id}`),
        searchData(`/api/compositeProducts/${id}`),
      ]);

      sessionStorage.setItem(
        "dataProductMaterials",
        JSON.stringify(dataProductMaterials)
      );
      sessionStorage.setItem(
        "dataCompositeProduct",
        JSON.stringify(dataCompositeProduct)
      );
      let data = [...dataProductMaterials, ...dataCompositeProduct];

      loadTableMaterials(data, visible);
    } catch (error) {
      console.error("Error loading data:", error);
    }
  };

  const loadTableMaterials = (data, visible) => {
    tblConfigMaterials = $("#tblConfigMaterials").dataTable({
      destroy: true,
      autoWidth: false,
      fixedHeader: true,
      scrollY: "400px",
      scrollCollapse: true,
      pageLength: 50,
      data: data,
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
          render: function (data) {
            return parseFloat(data).toLocaleString("es-CO", {
              maximumFractionDigits: 8,
            });
          },
        },
        {
          title: "Materia Prima Alterna",
          data: null,
          className: "uniqueClassName dt-head-center",
          visible: visible,
          render: function (data) {
            let alternal_material = '';

            if (data.id_alternal_material >= 0)
              alternal_material = `<a href="javascript:;">
                    <i id="${data.id_alternal_material}" class="${data.id_alternal_material != 0
                  ? "fas fa-check-square"
                  : "fa fa-window-close"
                }" data-toggle='tooltip' title='${data.alternal_material
                }' style="font-size:25px; color: ${data.id_alternal_material == 0 ? "#ff0000" : "#7bb520"
                };"></i>
                  </a>`;
            
            return alternal_material;
          },
        },
        {
          title: "Acciones",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: function (data) {
            let action;

            if (visible == true) {
              action = `<a href="javascript:;" <i id="updt-${
              data.id_product_material != 0
                ? data.id_product_material
                : data.id_composite_product
            }" class="bx bx-edit-alt ${
              data.id_product_material != 0
                ? "updateMaterials"
                : "updateComposite"
            }" data-toggle='tooltip' title='Actualizar Materia Prima' style="font-size: 30px;"></i></a>
                        <a href="javascript:;" <i id="${
                          data.id_product_material != 0
                            ? data.id_product_material
                            : data.id_composite_product
                        }" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Materia Prima' style="font-size: 30px;color:red" onclick="deleteMaterial(${
              data.id_product_material != 0 ? "1" : "2"
            })"></i></a>
              <a href="javascript:;" <i id="ext-${
                data.id_product_material
              }" class="bx bi bi-sliders alternalMaterial" data-toggle='tooltip' title='Materia Prima Alterna' style="font-size: 30px;color:#d36e17;"></i></a>
            `;
            } else {
              action = `<a href="javascript:;" <i id="${
              data.id_product_material != 0
                ? data.id_product_material
                : data.id_composite_product
            }" class="bx bx-edit-alt ${
              data.id_product_material != 0
                ? "updateMaterials"
                : "updateComposite"
            }" data-toggle='tooltip' title='Actualizar Materia Prima' style="font-size: 30px;"></i></a>
                        <a href="javascript:;" <i id="${
                          data.id_product_material != 0
                            ? data.id_product_material
                            : data.id_composite_product
                        }" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Materia Prima' style="font-size: 30px;color:red" onclick="deleteMaterial(${
              data.id_product_material != 0 ? "1" : "2"
            })"></i></a>`;
            }

            return action;
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

        for (i = 0; i < display.length; i++) {
          quantity += parseFloat(data[display[i]].quantity);
        }

        $(this.api().column(4).footer()).html(
          quantity.toLocaleString("es-CO", {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
          })
        );
      },
    });
  };
});
