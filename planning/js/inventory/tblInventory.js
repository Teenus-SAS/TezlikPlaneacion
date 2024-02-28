$(document).ready(function () {
  sessionStorage.removeItem("products");
  // Obtener Inventarios
  loadInventory = async () => { 
    try {
      const [inventory, dataProductsMaterials, dataUnitSales] = await Promise.all([
        searchData('/api/inventory'),
        searchData(`/api/allProductsMaterials`),
        searchData(`/api/unitSales`),
      ]);

      // dataProducts = JSON.stringify(inventory.products);  
      data = inventory;
      products = inventory.products;
      materials = inventory.rawMaterials;
      productsMaterials = dataProductsMaterials;
      unitsSales = dataUnitSales;

      inventoryIndicator(products);
      $('#products').click();      
    } catch (error) {
      console.error('Error loading data:', error);
    }
  };

  loadInventory();

  // Seleccionar Categoria
  $(".selectNavigation").click(function (e) {
    e.preventDefault();

    // Ocultar card formulario Analisis Inventario ABC
    $(".cardAddMonths").hide(800);
    $(".cardBtnAddMonths").hide(800);

    // Productos
    if (this.id == 'products') {
      $(".cardBtnAddMonths").show(800);
      data = getInventory(products);
      inventoryIndicator(products);
      data["visible"] = true;
    }
    // Materias Prima
    else if (this.id == 'materials') {
      data = getInventory(materials);
      data["visible"] = false;
    }
    // Todos
    // else if (value == 3) {
    //   dataProducts = getInventory(products);
    //   dataMaterials = getInventory(materials);

    //   data = dataProducts.concat(dataMaterials);
    //   data["visible"] = false;
    // }
    loadTable(data);
  });

  getInventory = (data) => {
    let dataInventory = [];
    for (i = 0; i < data.length; i++) {
      data[i].classification
        ? (classification = data[i].classification)
        : (classification = "");

      dataInventory.push({
        reference: data[i].reference,
        description: data[i].descript,
        abbreviation: data[i].abbreviation,
        quantity: data[i].quantity,
        reserved: data[i].reserved,
        minimum_stock: data[i].minimum_stock,
        // price: data[i].price,
        classification,
      });
    }

    return dataInventory;
  };

  const inventoryIndicator = (data) => {
    let totalQuantity = 0;
    let rotation = 0;
    let average = 0;
    let totalSales = 0;
    let coverage = 0;
    let available = 0;
    
    totalQuantity = data.reduce((acc, obj) => acc + obj.quantity, 0);
    average = totalQuantity / data.length;

    unitsSales.forEach(item => {
      for (const month in item) {
        if (month !== 'id_unit_sales' && month !== 'id_product' && month !== 'product' && month !== 'year' && month !== 'average') {
          totalSales += item[month];
        }
      }
    });

    rotation =  totalSales / average;
    coverage =  totalQuantity / (average / 365);
    available = data.reduce((acc, obj) => acc + (obj.quantity - obj.reserved), 0);

    $('#lblTotal').html(` Inv Total: ${totalQuantity.toLocaleString('es-CO', { maximumFractionDigits: 0 })}`);
    $('#lblRotation').html(` Rotacion: ${rotation.toLocaleString('es-CO', { maximumFractionDigits: 0 })}`);
    $('#lblCoverage').html(` Cobertura: ${coverage.toLocaleString('es-CO', { maximumFractionDigits: 0 })}`);
    $('#lblAvailable').html(` Disponible: ${available.toLocaleString('es-CO', { maximumFractionDigits: 0 })}`);
  }

  /* Cargar Tabla Inventarios */
  loadTable = (data) => {
    if ($.fn.dataTable.isDataTable("#tblInventories")) {
      $("#tblInventories").DataTable().destroy();
      $("#tblInventories").empty();
    }

    tblInventories = $("#tblInventories").dataTable({
      pageLength: 50,
      data: data,
      language: {
        url: "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json",
      },
      order: [[2, 'asc']],
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
          title: "Descripción",
          data: "description",
          className: "uniqueClassName dt-head-center",
        },
        {
          title: "Existencia",
          data: "quantity",
          className: "uniqueClassName dt-head-center",
          render: $.fn.dataTable.render.number(".", ",", 0),
        },
        {
          title: "Medida",
          data: "abbreviation",
          className: "uniqueClassName dt-head-center",
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
          title: "Stock",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: function (data) {
            data.unit == "UNIDAD"
              ? (number = data.minimum_stock.toLocaleString("es-CO", {
                  maximumFractionDigits: 0,
                }))
              : (number = data.minimum_stock.toLocaleString("es-CO", {
                  minimumFractionDigits: 2,
                }));
            return number;
          },
        },
        // {
        //   title: "Dias",
        //   data: "days",
        //   visible: visible,
        //   className: "uniqueClassName dt-head-center",
        //   render: $.fn.dataTable.render.number('.', ',', 0),
        // },
        {
          title: "Clasificación",
          data: null,
          className: "uniqueClassName dt-head-center",
          visible: data["visible"],
          render: function (data) {
            if (data.classification == "A") badge = "badge-success";
            else if (data.classification == "B") badge = "badge-info";
            else badge = "badge-danger";
            //else badge = "badge-light";
            return `<span class="badge ${badge}">${data.classification}</span>`;
            //return `<p>${data.classification}</p>`;
          },
        },
      ],
    });
  };
});
