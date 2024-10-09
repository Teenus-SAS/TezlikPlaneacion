$(document).ready(function () {
  const totalDefectiveUnits = localStorage.getItem("totalDefectiveUnits");
  const totalDeliveredQuantity = localStorage.getItem("totalDeliveredQuantity");

  let quality = totalDefectiveUnits / totalDeliveredQuantity;

  $("#kpiQualityOP").val(quality);
});
