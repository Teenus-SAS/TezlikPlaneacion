$(document).ready(function () {
  // Esperar hasta que el DataTable esté listo
  const checkDataTableReady = setInterval(() => {
    if (localStorage.getItem("dataTableReady") === "true") {
      // El DataTable está listo, ahora podemos acceder a las variables almacenadas en localStorage
      const totalDefectiveUnits =
        parseFloat(localStorage.getItem("totalDefectiveUnits")) || 0;
      const totalDeliveredQuantity =
        parseFloat(localStorage.getItem("totalDeliveredQuantity")) || 0;

      // Realizar otras operaciones o pasar los datos a otras funciones según sea necesario
      let quality = totalDefectiveUnits / totalDeliveredQuantity;
      $("#kpiQualityOP").val(quality);
      // Detener el setInterval ya que el DataTable está listo
      clearInterval(checkDataTableReady);
    }
  }, 500); // Verificar cada 500 ms si el DataTable está listo
});
