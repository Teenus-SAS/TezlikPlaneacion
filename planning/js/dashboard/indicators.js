const indicatorsGlobal = async () => {
  try {
    const response = await $.ajax({
      url: `/api/dashboardIndicators`,
    });

    const {
      productsOutStock,
      percentageMPLowStock,
      ordersNoProgramed,
      OrdersNoMP,
      percentage_dispatch,
    } = response;

    const formatPercentage = (value) =>
      value !== undefined ? value.toFixed(2).replace(".", ",") : "0";

    // Formatear los valores
    const formattedValues = {
      productsOutStock: formatPercentage(productsOutStock),
      mpOutStock: formatPercentage(percentageMPLowStock),
      ordersNoProgramed: formatPercentage(ordersNoProgramed),
      OrdersNoMP: formatPercentage(OrdersNoMP),
      OrdersDelivered: formatPercentage(percentage_dispatch),
    };

    // Actualizar el DOM
    $("#productStockout").text(
      `${response.totalProducts} | ${formattedValues.productsOutStock}%`
    );
    $("#productStockout").text(
      `${response.totalMPLowStock} | ${formattedValues.mpOutStock}%`
    );
    $("#ordersNoProgramed").text(`${formattedValues.ordersNoProgramed}%`);
    $("#ordersNoMP").text(`${formattedValues.OrdersNoMP}%`);
    $("#ordersDelivered").text(`${formattedValues.OrdersDelivered}%`);
  } catch (error) {
    console.error("Error fetching dashboard indicators:", error);
    toastr.error("Error al obtener los indicadores. Intente nuevamente");
  }
};

indicatorsGlobal();
