$(document).ready(function () {
  // Extraer valores de unidades defectuosas y procesadas, eliminando la palabra "Und"
  let unitsDefects =
    parseFloat(
      $(".unitsDefects")
        .text()
        .replace(" Und", "")
        .replace(".", "")
        .replace(",", ".")
    ) || 0;
  let unitsProcessing =
    parseFloat(
      $(".unitsProcessing")
        .text()
        .replace(" Und", "")
        .replace(".", "")
        .replace(",", ".")
    ) || 0;

  // Verificar que los valores de 'unitsDefects' y 'unitsProcessing' sean válidos
  if (unitsDefects > 0 && unitsProcessing > 0) {
    // Calcular la calidad del proceso (porcentaje)
    let quality = ((unitsProcessing - unitsDefects) / unitsProcessing) * 100;

    // Mostrar el resultado con dos decimales en el elemento correspondiente
    $("#kpiQualityOP").text(`Q: ${quality.toFixed(2)}%`);
  } else {
    // Ocultar el elemento si los valores no son válidos
    $("#kpiQualityOP").hide();
  }
  /* $.ajax({
    url: "/api/kpisOP",
    data: "data",
    success: function (data) {
      if (data) $("#kpiQualityOP").text(`Q: ${data.toFixed(2)}%`);
      else $("#kpiQualityOP").hide();
    },
  }); */
});
