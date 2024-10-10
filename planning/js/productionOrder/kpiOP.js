$(document).ready(function () {
  $.ajax({
    url: "/api/kpisOP",
    data: "data",
    success: function (data) {
      if (data) $("#kpiQualityOP").text(`Q: ${data}%`);
      else $("#kpiQualityOP").hide();
    },
  });
});
