$(document).ready(function () {
  // Seleccionar los elementos <th> a observar
  const targetDefects = document.querySelector(".unitsDefects");
  const targetProcessing = document.querySelector(".unitsProcessing");

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

        // Realizar el cálculo del KPI de calidad
        if (unitsProcessing > 0) {
          const quality = 1-(unitsDefects / unitsProcessing) * 100;
          $("#kpiQualityOP")
            .text(`QC: ${quality.toFixed(2)}%`)
            .show();
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
