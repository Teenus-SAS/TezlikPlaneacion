indicatorsGlobal = () => {
  $.ajax({
    url: `/api/dashboardIndicators`,
    success: function (response) {
      const percentage = (response.percentage_zero_quantity * 100)
        .toFixed(2)
        .replace(".", ",");
      $("#productStockout").text(percentage + `%`);
    },
  });
};

indicatorsGlobal();
