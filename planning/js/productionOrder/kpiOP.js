$(document).ready(function () {
  // Seleccionar los elementos <th> a observar
  const targetDefects = document.querySelector(".unitsDefects");
  const targetProcessing = document.querySelector(".unitsProcessing");
  const costPayroll = document.querySelector(".costPayroll");

  // Crear una instancia de MutationObserver
  const observer = new MutationObserver(function (mutationsList, observer) {
    for (const mutation of mutationsList) {
      if (mutation.type === "childList") {
        console.log(
          "Contenido de las columnas .unitsDefects o .unitsProcessing cambió."
        );

        // Capturar los nuevos valores
        const unitsDefects =
          parseFloat(
            $(".unitsDefects")
              .text()
              .replace(" Und", "")
              .replace(".", "")
              .replace(",", ".")
          ) || 0;
        const unitsProcessing =
          parseFloat(
            $(".unitsProcessing")
              .text()
              .replace(" Und", "")
              .replace(".", "")
              .replace(",", ".")
          ) || 0;
        const costPayroll =
          parseFloat(
            $(".costPayroll")
              .text()
              .replace(" Und", "")
              .replace(".", "")
              .replace(",", ".")
          ) || 0;

        // Realizar el cálculo del KPI de calidad
        if (unitsProcessing > 0) {
          let quality = 1 - unitsDefects / unitsProcessing;
          quality = quality * 100;
          $("#kpiQualityOP")
            .text(`QC: ${quality.toFixed(2)}%`)
            .show();
          $("#costPayroll").val(`CMO: $${costPayroll.toFixed(0)}`);
        } else {
          $("#kpiQualityOP").hide();
        }
      }
    }
  });

  // Opciones de configuración para observar los cambios en el contenido de los elementos
  const config = { childList: true };

  // Iniciar la observación en las columnas específicas
  if (targetDefects) observer.observe(targetDefects, config);
  if (targetProcessing) observer.observe(targetProcessing, config);
});
