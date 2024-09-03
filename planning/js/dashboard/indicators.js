indicatorsGlobal = () => {
  $.ajax({
    url: `/api/dashboardIndicators`,
    success: function (response) {
      $("#productStockout").text(response.percentage_zero_quantity + `%`);
    },
  });
};

indicatorsGlobal();
