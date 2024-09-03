indicatorsGlobal = (id) => {
  $.ajax({
    url: `/api/indicators/${id}`,
    success: function (response) {
      if (response[0].newContacts != null)
        $("#productStockout").html(response[0].newContacts);
      else $("#productStockout").html("0");
    },
  });
};
