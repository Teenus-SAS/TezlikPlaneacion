$(document).ready(function () {
  // Seleccionar los elementos <th> a observar
  const targetDefects = document.querySelector(".unitsDefects");
  const targetProcessing = document.querySelector(".unitsProcessing");
  const targetCostPayroll = document.querySelector(".costPayroll");
  const targetCostMaterials = document.querySelector(".costMaterials");
  const targetCostIndirect = document.querySelector(".costIndirect");

  // Verifica si los elementos existen antes de iniciar el observador
  if (targetDefects && targetProcessing && targetCostPayroll) {
    // Crear una instancia de MutationObserver
    const observer = new MutationObserver(function (mutationsList) {
      mutationsList.forEach((mutation) => {
        if (mutation.type === "childList") {
          console.log(
            "Contenido de las columnas .unitsDefects, .unitsProcessing o .costPayroll cambió."
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
                .replace("$", "")
                .replace(".", "")
                .replace(",", ".")
            ) || 0;
          const costMaterials =
            parseFloat(
              $(".costMaterials")
                .text()
                .replace("$", "")
                .replace(".", "")
                .replace(",", ".")
            ) || 0;
          const costIndirect =
            parseFloat(
              $(".costIndirect")
                .text()
                .replace("$", "")
                .replace(".", "")
                .replace(",", ".")
            ) || 0;
          const costMaterialsUnit =
            parseFloat(
              $(".costMaterialsUnit")
                .text()
                .replace("$", "")
                .replace(".", "")
                .replace(",", ".")
            ) || 0;

          // Realizar el cálculo del KPI de calidad
          if (unitsProcessing > 0) {
            //Total Cost
            let totalCost = costPayroll + costMaterials + costIndirect;
            let costMPUnit = costMaterials / unitsProcessing;
            let costPayrollUnit = costPayroll / unitsProcessing;
            let costIndirectunit = costIndirect / unitsProcessing;
            let totalCostUnit = costPayrollUnit + costIndirectunit;
            //calc quality
            let quality = 1 - unitsDefects / unitsProcessing;
            quality = quality * 100;
            $("#kpiQualityOP")
              .text(`QC: ${quality.toFixed(2)}%`)
              .show();

            $("#kpiCostPayrollUnit").text(
              `MO: $${costPayrollUnit.toLocaleString("es-CO", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
              })}`
            );

            $("#kpiCostPayroll").text(
              `MO: $${costPayroll.toLocaleString("es-CO", {
                minimumFractionDigits: 0,
              })}`
            );

            $("#kpiCostMaterials").text(
              `MP: $${costMaterials.toLocaleString("es-CO", {
                minimumFractionDigits: 0,
              })}`
            );
            $("#kpiCostMaterialsUnit").text(
              `MP: $${costMPUnit.toLocaleString("es-CO", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
              })}`
            );

            $("#kpiIndirectCost").text(
              `CI: $${costIndirect.toLocaleString("es-CO", {
                minimumFractionDigits: 0,
              })}`
            );
            $("#kpiIndirectCostUnit").text(
              `CI: $${costIndirectunit.toLocaleString("es-CO", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
              })}`
            );

            $("#kpiTotalCost").text(
              `CT: $${totalCost.toLocaleString("es-CO", {
                minimumFractionDigits: 0,
              })}`
            );
            $("#kpiTotalCostUnit").text(
              `CT: $${totalCostUnit.toLocaleString("es-CO", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
              })}`
            );
            $("#titleGeneralCost").show();
            $("#titleUnitCost").show();
            $("#lineDivTitleCost").show();
          } else {
            $("#kpiQualityOP").hide();
            $("#titleGeneralCost").hide();
            $("#titleUnitCost").hide();
            $("#lineDivTitleCost").hide();
          }
        }
      });
    });

    // Opciones de configuración para observar los cambios en el contenido de los elementos
    const config = { childList: true, subtree: true };

    // Iniciar la observación en las columnas específicas
    observer.observe(targetDefects, config);
    observer.observe(targetProcessing, config);
    observer.observe(targetCostPayroll, config);
    observer.observe(targetCostMaterials, config);
    observer.observe(targetCostIndirect, config);
  } else {
    console.error("No se encontraron los elementos para observar.");
  }
});
