$(document).ready(function () {
  $.ajax({
    url: "/api/kpisOP",
    data: "data",
    success: function (data) {
      if (data) $("#kpiQualityOP").text(`Q: ${data.toFixed(2)}%`);
      else $("#kpiQualityOP").hide();
    },
  });
});
