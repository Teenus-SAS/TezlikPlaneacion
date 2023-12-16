$(document).ready(function () {
  sessionStorage.removeItem("products");
  // Obtener Inventarios
  loadInventory = async () => {
    await fetch(`/api/inventory`)
      .then((response) => response.text())
      .then((data) => {
        data = JSON.parse(data);
        // Guardar productos
        dataProducts = JSON.stringify(data.products);  

        products = data.products;
        materials = data.rawMaterials;

        $('#products').click();
      });
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
          className: "uniqueClassName",
          render: function (data, type, full, meta) {
            return meta.row + 1;
          },
        },
        {
          title: "Referencia",
          data: "reference",
          className: "uniqueClassName",
        },
        {
          title: "Descripción",
          data: "description",
          className: "uniqueClassName",
        },
        {
          title: "Existencia",
          data: "quantity",
          className: "uniqueClassName",
          render: $.fn.dataTable.render.number(".", ",", 0),
        },
        {
          title: "Medida",
          data: "abbreviation",
          className: "classCenter",
        },
        {
          title: "Reservado",
          data: null,
          className: "uniqueClassName",
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
          className: "uniqueClassName",
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
        //   className: "uniqueClassName",
        //   render: $.fn.dataTable.render.number('.', ',', 0),
        // },
        {
          title: "Clasificación",
          data: null,
          className: "uniqueClassName",
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
