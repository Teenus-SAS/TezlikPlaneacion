indicatorsGlobal = () => {
  $.ajax({
    url: `/api/dashboardIndicators/`,
    success: function (response) {
      $("#productStockout").html(response[0].newContacts);
    },
  });
};
