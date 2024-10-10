$(document).ready(function () {
  $.ajax({
    type: "POST",
    url: "/api/kpis",
    data: "data",
    success: function (data) {
      let quality = data.totalDefectiveUnits / data.totalDeliveredQuantity;
      $("#kpiQualityOP").val(quality);
    },
  });
});
