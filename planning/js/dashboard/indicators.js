const formatPercentage = (value) => value.toFixed(2).replace(".", ",");

const indicatorsGlobal = async () => {
  try {
    const response = await $.ajax({
      url: `/api/dashboardIndicators`,
    });

    const { productsOutStock, ordersNoProgramed, OrdersNoMP, OrdersDelivered } =
      response;

    // Formatear los valores
    const formattedValues = {
      productsOutStock: formatPercentage(productsOutStock),
      ordersNoProgramed: formatPercentage(ordersNoProgramed),
      OrdersNoMP: formatPercentage(OrdersNoMP),
      OrdersDelivered: formatPercentage(OrdersDelivered),
    };

    // Actualizar el DOM
    $("#productStockout").text(`${formattedValues.productsOutStock}%`);
    $("#ordersNoProgramed").text(`${formattedValues.ordersNoProgramed}%`);
    $("#ordersNoMP").text(`${formattedValues.OrdersNoMP}%`);
    $("#ordersDelivered").text(`${formattedValues.OrdersDelivered}%`);
  } catch (error) {
    console.error("Error fetching dashboard indicators:", error);
    toastr.error("Error al obtener los indicadores. Intente nuevamente");
  }
};

indicatorsGlobal();
