const indicatorsGlobal = async () => {
  try {
    const response = await $.ajax({
      url: `/api/dashboardIndicators`,
    });

    const {
      productsOutStock,
      percentageMPLowStock,
      ordersActive,
      ordersNoProgramed,
      totalOrdersNoProgrammed,
      OrdersNoMP,
      percentage_dispatch,
      orders_dispatch,
    } = response;

    const formatPercentage = (value) =>
      value !== undefined ? value.toFixed(2).replace(".", ",") : "0";

    // Formatear los valores
    const formattedValues = {
      productsOutStock: formatPercentage(productsOutStock),
      mpOutStock: formatPercentage(percentageMPLowStock),
      ordersActive: formatPercentage(ordersActive),
      ordersNoProgramed: formatPercentage(ordersNoProgramed),
      totalOrdersNoProgrammed: formatPercentage(totalOrdersNoProgrammed),
      OrdersNoMP: formatPercentage(OrdersNoMP),
      percentOrdersDelivered: formatPercentage(percentage_dispatch),
      totalOrdersDispatch: formatPercentage(orders_dispatch),
    };

    // Actualizar el DOM
    $("#productStockout").text(`${response.totalProducts}`);
    $("#productPercentStockOut").text(`${formattedValues.productsOutStock}%`);

    $("#mpOutStock").text(`${response.totalMPLowStock}`);
    $("#mpPercentageOutStock").text(`${formattedValues.mpOutStock}%`);

    $("#ordersActive").text(`${formattedValues.ordersActive}`);

    $("#ordersNoProgramed").text(`${formattedValues.totalOrdersNoProgrammed}`);
    $("#percentOrdersNoProgramed").text(
      `${formattedValues.ordersNoProgramed}%`
    );

    $("#ordersNoMP").text(`${formattedValues.OrdersNoMP}%`);

    $("#totalOrdersDispatch").text(`${formattedValues.totalOrdersDispatch}`);
    $("#percentOrdersDelivered").text(
      `${formattedValues.percentOrdersDelivered}%`
    );
  } catch (error) {
    console.error("Error fetching dashboard indicators:", error);
    toastr.error("Error al obtener los indicadores. Intente nuevamente");
  }
};

indicatorsGlobal();
