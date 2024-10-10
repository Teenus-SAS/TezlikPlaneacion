$(document).ready(function () {
  unitsDefects = $(".unitsDefects").val();
  unitsProcessing = $(".unitsProcessing").val();

  if (unitsDefects && unitsProcessing)
    $("#kpiQualityOP").text(`Q: ${data.toFixed(2)}%`);
  else $("#kpiQualityOP").hide();

  /* $.ajax({
    url: "/api/kpisOP",
    data: "data",
    success: function (data) {
      if (data) $("#kpiQualityOP").text(`Q: ${data.toFixed(2)}%`);
      else $("#kpiQualityOP").hide();
    },
  }); */
});
