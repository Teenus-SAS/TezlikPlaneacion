$(document).ready(function () {
  sessionStorage.removeItem("products");
  // Obtener Inventarios
  // loadInventory = async () => {
  //   try {
  //     const [inventory, dataProductsMaterials, dataUnitSales] =
  //       await Promise.all([
  //         searchData("/api/inventory"),
  //         searchData(`/api/allProductsMaterials`),
  //         searchData(`/api/unitSales`),
  //       ]);

  //     data = inventory;
  //     products = inventory.products;
  //     materials = inventory.rawMaterials;
  //     productsMaterials = dataProductsMaterials;
  //     unitsSales = dataUnitSales;

  //     inventoryIndicator(products);
  //     $("#products").click();
  //   } catch (error) {
  //     console.error("Error loading data:", error);
  //   }
  // };

  // Obtener Inventarios
  loadInventory = async () => {
    try {
      const [inventory, dataProductsMaterials, dataUnitSales] =
        await Promise.all([
          searchData("/api/inventory"),
          searchData(`/api/allProductsMaterials`),
          searchData(`/api/unitSales`),
        ]);

      data = inventory;
      products = inventory.products;
      materials = inventory.rawMaterials;
      productsMaterials = dataProductsMaterials;
      unitsSales = dataUnitSales;

      inventoryIndicator(products);
      $("#products").click();
    } catch (error) {
      console.error("Error loading data:", error);
    }
  };
  loadInventory();

  // $(".selectNavigation").click(function (e) {
  //   e.preventDefault();

  //   // Ocultar elementos comunes
  //   const hideElements = [
  //     ".cardAddMonths",
  //     ".cardInventoryABC",
  //     ".cardBtnAddMonths",
  //   ];

  //   hideElements.forEach((selector) => $(selector).hide());

  //   const options = {
  //     products: {
  //       show: [".cardBtnAddMonths"],
  //       data: () => {
  //         let data = getInventory(products);
  //         inventoryIndicator(products);
  //         data["visible"] = true;
  //         return data;
  //       },
  //     },
  //     materials: {
  //       show: [],
  //       data: () => {
  //         let data = getInventory(materials);
  //         data["visible"] = false;
  //         return data;
  //       },
  //     },
  //   };

  //   if (options[this.id]) {
  //     const { show, data } = options[this.id];
  //     show.forEach((selector) => $(selector).show(800));
  //     loadTable(data());
  //   }
  // });

  $(".selectNavigation").click(function (e) {
    e.preventDefault();

    // Elementos que deben ocultarse siempre
    const hideElements = [
      ".cardAddMonths",
      ".cardInventoryABC",
      ".cardBtnAddMonths",
    ];

    const options = {
      products: {
        show: [".cardBtnAddMonths"],
        data: () => prepareInventory(products, true),
      },
      materials: {
        show: [],
        data: () => prepareInventory(materials, false),
      },
    };

    // Si el ID está mapeado en las opciones
    const selectedOption = options[this.id];
    if (selectedOption) {
      handleVisibility(hideElements, selectedOption.show); // Controla la visibilidad
      loadTable(selectedOption.data()); // Carga la tabla
    } else {
      console.warn(`No se encontraron opciones para el ID: ${this.id}`);
    }
  });

  // Función para manejar visibilidad de elementos
  function handleVisibility(hideSelectors, showSelectors) {
    hideSelectors.forEach((selector) => $(selector).hide());
    showSelectors.forEach((selector) => $(selector).show(800));
  }

  // Función para preparar datos del inventario
  function prepareInventory(data, isVisible) {
    const inventoryData = getInventory(data);
    inventoryIndicator(data); // Asume que modifica o usa `data` directamente
    inventoryData["visible"] = isVisible;
    return inventoryData;
  }

  // const getInventory = (data) => {
  //   let dataInventory = [];
  //   for (i = 0; i < data.length; i++) {
  //     data[i].classification
  //       ? (classification = data[i].classification)
  //       : (classification = "");

  //     dataInventory.push({
  //       reference: data[i].reference,
  //       description: data[i].descript,
  //       abbreviation: data[i].abbreviation,
  //       quantity: data[i].quantity,
  //       reserved: data[i].reserved,
  //       minimum_stock: data[i].minimum_stock,
  //       classification,
  //     });
  //   }

  //   return dataInventory;
  // };

  const getInventory = (data) => {
    return data.map((item) => {
      const {
        reference,
        descript: description,
        abbreviation,
        quantity,
        reserved,
        minimum_stock,
        classification = "", // Valor predeterminado si no existe `classification`
      } = item;

      return {
        reference,
        description,
        abbreviation,
        quantity,
        reserved,
        minimum_stock,
        classification,
      };
    });
  };

  // const inventoryIndicator = (data) => {
  //   let totalQuantity = 0;
  //   let rotation = 0;
  //   let average = 0;
  //   let totalSales = 0;
  //   let coverage = 0;
  //   let available = 0;

  //   totalQuantity = data.reduce(
  //     (acc, obj) => acc + parseFloat(obj.quantity),
  //     0
  //   );
  //   average = totalQuantity / data.length;

  //   unitsSales.forEach((item) => {
  //     for (const month in item) {
  //       if (
  //         month !== "id_unit_sales" &&
  //         month !== "id_product" &&
  //         month !== "product" &&
  //         month !== "year" &&
  //         month !== "average"
  //       ) {
  //         totalSales += item[month];
  //       }
  //     }
  //   });

  //   rotation = totalSales / average;
  //   coverage = totalQuantity / (average / 365);
  //   available = data.reduce(
  //     (acc, obj) => acc + (parseFloat(obj.quantity) - parseFloat(obj.reserved)),
  //     0
  //   );

  //   $("#lblTotal").html(
  //     ` Inv Total: ${totalQuantity.toLocaleString("es-CO", {
  //       maximumFractionDigits: 0,
  //     })}`
  //   );
  //   $("#lblRotation").html(
  //     ` Rotacion: ${rotation.toLocaleString("es-CO", {
  //       maximumFractionDigits: 0,
  //     })}`
  //   );
  //   $("#lblCoverage").html(
  //     ` Cobertura: ${coverage.toLocaleString("es-CO", {
  //       maximumFractionDigits: 0,
  //     })}`
  //   );
  //   $("#lblAvailable").html(
  //     ` Disponible: ${available.toLocaleString("es-CO", {
  //       maximumFractionDigits: 0,
  //     })}`
  //   );
  // };

  const inventoryIndicator = (data) => {
    let totalQuantity = 0;
    let rotation = 0;
    let average = 0;
    let totalSales = 0;
    let coverage = 0;
    let available = 0;

    totalQuantity = data.reduce(
      (acc, obj) => acc + parseFloat(obj.quantity),
      0
    );
    average = totalQuantity / data.length;

    unitsSales.forEach((item) => {
      for (const month in item) {
        if (
          month !== "id_unit_sales" &&
          month !== "id_product" &&
          month !== "product" &&
          month !== "year" &&
          month !== "average"
        ) {
          totalSales += item[month];
        }
      }
    });

    rotation = totalSales / average;
    coverage = totalQuantity / (average / 365);
    available = data.reduce(
      (acc, obj) => acc + (parseFloat(obj.quantity) - parseFloat(obj.reserved)),
      0
    );

    $("#lblTotal").html(
      ` Inv Total: ${totalQuantity.toLocaleString("es-CO", {
        maximumFractionDigits: 0,
      })}`
    );
    $("#lblRotation").html(
      ` Rotacion: ${rotation.toLocaleString("es-CO", {
        maximumFractionDigits: 0,
      })}`
    );
    $("#lblCoverage").html(
      ` Cobertura: ${coverage.toLocaleString("es-CO", {
        maximumFractionDigits: 0,
      })}`
    );
    $("#lblAvailable").html(
      ` Disponible: ${available.toLocaleString("es-CO", {
        maximumFractionDigits: 0,
      })}`
    );
  };
  /* Cargar Tabla Inventarios */
  // loadTable = (data) => {
  //   if ($.fn.dataTable.isDataTable("#tblInventories")) {
  //     $("#tblInventories").DataTable().destroy();
  //     $("#tblInventories").empty();
  //   }

  //   tblInventories = $("#tblInventories").dataTable({
  //     fixedHeader: true,
  //     scrollY: "400px",
  //     scrollCollapse: true,
  //     pageLength: 50,
  //     fixedHeader: true,
  //     scrollY: "400px",
  //     scrollCollapse: true,
  //     data: data,
  //     language: {
  //       url: "/assets/plugins/i18n/Spanish.json",
  //     },
  //     order: [[2, "asc"]],
  //     columns: [
  //       {
  //         title: "No.",
  //         data: null,
  //         className: "uniqueClassName dt-head-center",
  //         render: function (data, type, full, meta) {
  //           return meta.row + 1;
  //         },
  //       },
  //       {
  //         title: "Referencia",
  //         data: "reference",
  //         className: "uniqueClassName dt-head-center",
  //       },
  //       {
  //         title: "Descripción",
  //         data: "description",
  //         className: "uniqueClassName dt-head-center",
  //       },
  //       /* {
  //         title: "Medida",
  //         data: "abbreviation",
  //         className: "uniqueClassName dt-head-center",
  //       }, */
  //       {
  //         title: "Existencia",
  //         data: null,
  //         //data: "quantity",
  //         className: "uniqueClassName dt-head-Right",
  //         //render: $.fn.dataTable.render.number(".", ",", 0),
  //         render: function (data, type, row) {
  //           const formattedQuantity = $.fn.dataTable.render
  //             .number(".", ",", 0)
  //             .display(data.quantity);
  //           const formattedAbbreviation =
  //             data.abbreviation.charAt(0).toUpperCase() +
  //             data.abbreviation.slice(1).toLowerCase();
  //           return `${formattedQuantity} ${formattedAbbreviation}`;
  //         },
  //       },
  //       {
  //         title: "Reservado",
  //         data: null,
  //         className: "uniqueClassName dt-head-center",
  //         render: function (data) {
  //           data.abbreviation == "UND"
  //             ? (number = parseFloat(data.reserved).toLocaleString("es-CO", {
  //               maximumFractionDigits: 0,
  //             }))
  //             : (number = parseFloat(data.reserved).toLocaleString("es-CO", {
  //               minimumFractionDigits: 2,
  //               maximumFractionDigits: 2,
  //             }));
  //           return number;
  //         },
  //       },
  //       {
  //         title: "Stock",
  //         data: null,
  //         className: "uniqueClassName dt-head-center",
  //         render: function (data) {
  //           data.abbreviation == "UND"
  //             ? (number = parseFloat(data.minimum_stock).toLocaleString(
  //               "es-CO",
  //               {
  //                 maximumFractionDigits: 0,
  //               }
  //             ))
  //             : (number = parseFloat(data.minimum_stock).toLocaleString(
  //               "es-CO",
  //               {
  //                 minimumFractionDigits: 2,
  //                 maximumFractionDigits: 2,
  //               }
  //             ));
  //           return number;
  //         },
  //       },
  //       {
  //         title: "Clasificación",
  //         data: null,
  //         className: "uniqueClassName dt-head-center",
  //         visible: data["visible"],
  //         render: function (data) {
  //           if (data.classification == "A") badge = "badge-success";
  //           else if (data.classification == "B") badge = "badge-info";
  //           else badge = "badge-danger";
  //           //else badge = "badge-light";
  //           return `<span class="badge ${badge}" style="font-size: large;">${data.classification}</span>`;
  //           //return `<p>${data.classification}</p>`;
  //         },
  //       },
  //     ],
  //     headerCallback: function (thead, data, start, end, display) {
  //       $(thead).find("th").css({
  //         "background-color": "#386297",
  //         color: "white",
  //         "text-align": "center",
  //         "font-weight": "bold",
  //         padding: "10px",
  //         border: "1px solid #ddd",
  //       });
  //     },
  //   });
  // };
  loadTable = (data) => {
    if ($.fn.dataTable.isDataTable("#tblInventories")) {
      $("#tblInventories").DataTable().destroy();
      $("#tblInventories").empty();
    }

    tblInventories = $("#tblInventories").dataTable({
      fixedHeader: true,
      scrollY: "400px",
      scrollCollapse: true,
      pageLength: 50,
      fixedHeader: true,
      scrollY: "400px",
      scrollCollapse: true,
      data: data,
      language: {
        url: "/assets/plugins/i18n/Spanish.json",
      },
      order: [[2, "asc"]],
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
          data: null,
          //data: "quantity",
          className: "uniqueClassName dt-head-Right",
          //render: $.fn.dataTable.render.number(".", ",", 0),
          render: function (data, type, row) {
            const formattedQuantity = $.fn.dataTable.render
              .number(".", ",", 0)
              .display(data.quantity);
            const formattedAbbreviation =
              data.abbreviation.charAt(0).toUpperCase() +
              data.abbreviation.slice(1).toLowerCase();
            return `${formattedQuantity} ${formattedAbbreviation}`;
          },
        },
        {
          title: "Reservado",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: function (data) {
            data.abbreviation == "UND"
              ? (number = parseFloat(data.reserved).toLocaleString("es-CO", {
                maximumFractionDigits: 0,
              }))
              : (number = parseFloat(data.reserved).toLocaleString("es-CO", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
              }));
            return number;
          },
        },
        {
          title: "Stock",
          data: null,
          className: "uniqueClassName dt-head-center",
          render: function (data) {
            data.abbreviation == "UND"
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
          title: "Clasificación",
          data: null,
          className: "uniqueClassName dt-head-center",
          visible: data["visible"],
          render: function (data) {
            if (data.classification == "A") badge = "badge-success";
            else if (data.classification == "B") badge = "badge-info";
            else badge = "badge-danger";
            return `<span class="badge ${badge}" style="font-size: large;">${data.classification}</span>`;
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
    });
  };
});
