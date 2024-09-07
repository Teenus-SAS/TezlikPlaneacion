$(document).ready(function () {
  /* Seleccion producto */

  $("#refProduct").change(function (e) {
    e.preventDefault();
    let id = this.value; 
    
    $("#selectNameProduct option").removeAttr("selected");
    $(`#selectNameProduct option[value=${id}]`).prop("selected", true);

    let dataProducts = JSON.parse(sessionStorage.getItem('dataProducts'));
    let compositeProduct = dataProducts.filter(item => item.composite == 1 && item.id_product != this.value); 

    populateOptions("#refCompositeProduct", compositeProduct, "reference");
    populateOptions("#compositeProduct", compositeProduct, "product");

    $('.cardAddMaterials').hide(800);
    loadAllDataMaterials(id);
    loadTblPlanCiclesMachine(id);
    loadTblRoutes(id);
    loadTblProductPlans(id);
  });

  $("#selectNameProduct").change(function (e) {
    e.preventDefault();
    let id = this.value; 
    
    $("#refProduct option").removeAttr("selected");
    $(`#refProduct option[value=${id}]`).prop("selected", true);

    let dataProducts = JSON.parse(sessionStorage.getItem('dataProducts'));
    let compositeProduct = dataProducts.filter(item => item.composite == 1 && item.id_product != this.value); 

    populateOptions("#refCompositeProduct", compositeProduct, "reference");
    populateOptions("#compositeProduct", compositeProduct, "product");

    $('.cardAddMaterials').hide(800);
    loadAllDataMaterials(id);
    loadTblPlanCiclesMachine(id);
    loadTblRoutes(id);
    loadTblProductPlans(id);
  });

  /* Cargue tabla de Productos Materiales */
  loadAllDataMaterials = async (id) => {
    try {
      const [dataProductMaterials, dataCompositeProduct] = await Promise.all([
        searchData(`/api/productsMaterials/${id}`),
        searchData(`/api/compositeProducts/${id}`)
      ]);

      sessionStorage.setItem('dataProductMaterials', JSON.stringify(dataProductMaterials));
      sessionStorage.setItem('dataCompositeProduct', JSON.stringify(dataCompositeProduct));  
      let data = [...dataProductMaterials, ...dataCompositeProduct];

      loadTableMaterials(data);
    } catch (error) {
      console.error('Error loading data:', error);
    }
  };

  const loadTableMaterials = (data) => {
    tblConfigMaterials = $("#tblConfigMaterials").dataTable({
      destroy: true,
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
            return parseFloat(data).toLocaleString('es-CO', { maximumFractionDigits: 8 });
          },
        },
        {
          title: "Acciones",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: function (data) {
            return `<a href="javascript:;" <i id="${data.id_product_material != 0 ? data.id_product_material : data.id_composite_product}" class="bx bx-edit-alt ${data.id_product_material != 0 ? 'updateMaterials' : 'updateComposite'}" data-toggle='tooltip' title='Actualizar Materia Prima' style="font-size: 30px;"></i></a>
                        <a href="javascript:;" <i id="${data.id_product_material != 0 ? data.id_product_material : data.id_composite_product}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Materia Prima' style="font-size: 30px;color:red" onclick="deleteMaterial(${data.id_product_material != 0 ? '1' : '2'})"></i></a>`;
            // `<a href="javascript:;" <i id="updt-${data}" class="bx bx-edit-alt updateMaterials" data-toggle='tooltip' title='Actualizar Materia Prima' style="font-size: 30px;"></i></a>
            //   <a href="javascript:;" <i id="${data}" class="mdi mdi-delete-forever" data-toggle='tooltip' title='Eliminar Materia Prima' style="font-size: 30px;color:red" onclick="deleteMaterial(${data})"></i></a>`;
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
});
