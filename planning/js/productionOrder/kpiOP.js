$(document).ready(function () {
  // Seleccionar los elementos <th> a observar
  const targets = {
    defects: document.querySelector(".unitsDefects"),
    processing: document.querySelector(".unitsProcessing"),
    payroll: document.querySelector(".costPayroll"),
    materials: document.querySelector(".costMaterials"),
    indirect: document.querySelector(".costIndirect"),
  };

  // Verifica si los elementos existen antes de iniciar el observador
  if (Object.values(targets).every((target) => target)) {
    // Crear una instancia de MutationObserver
    const observer = new MutationObserver(function (mutationsList) {
      mutationsList.forEach((mutation) => {
        if (mutation.type === "childList") {
          console.log("Se detectaron cambios en las columnas observadas.");

          // Funciones para limpiar y formatear valores
          const parseCurrency = (value) =>
            parseFloat(value.replace(/[\$\.]/g, "").replace(",", ".")) || 0;

          const formatCurrency = (value) =>
            `$${value.toLocaleString("es-CO", {
              maximumFractionDigits: 2,
            })}`;

          // Capturar los nuevos valores
          const unitsDefects =
            parseFloat($(".unitsDefects").text().replace(" Und", "")) || 0;

          const unitsProcessing =
            parseFloat($(".unitsProcessing").text().replace(" Und", "")) || 0;

          const costPayroll = parseCurrency($(".costPayroll").text());
          const costMaterials = parseCurrency($(".costMaterials").text());
          const costIndirect = parseCurrency($(".costIndirect").text());

          if (unitsProcessing > 0) {
            // Calcular costos y KPI
            let totalCost = costPayroll + costMaterials + costIndirect;
            let costPayrollUnit = costPayroll / unitsProcessing;
            let costMaterialsUnit = costMaterials / unitsProcessing;
            let costIndirectUnit = costIndirect / unitsProcessing;
            let totalCostUnit =
              costPayrollUnit + costMaterialsUnit + costIndirectUnit;

            let quality = (1 - unitsDefects / unitsProcessing) * 100;

            // Actualizar valores en la interfaz
            $("#kpiQualityOP")
              .text(`QC: ${quality.toFixed(2)}%`)
              .show();
            $("#kpiCostPayroll").text(`MO: ${formatCurrency(costPayroll)}`);
            $("#kpiCostPayrollUnit").text(
              `MO: ${formatCurrency(costPayrollUnit)}`
            );
            $("#kpiCostMaterials").text(`MP: ${formatCurrency(costMaterials)}`);
            $("#kpiCostMaterialsUnit").text(
              `MP: ${formatCurrency(costMaterialsUnit)}`
            );
            $("#kpiIndirectCost").text(`CI: ${formatCurrency(costIndirect)}`);
            $("#kpiIndirectCostUnit").text(
              `CI: ${formatCurrency(costIndirectUnit)}`
            );
            $("#kpiTotalCost").text(`CT: ${formatCurrency(totalCost)}`);
            $("#kpiTotalCostUnit").text(`CT: ${formatCurrency(totalCostUnit)}`);

            // Mostrar títulos y divisores
            $("#titleGeneralCost, #titleUnitCost, #lineDivTitleCost").show();
          } else {
            // Ocultar KPI si no hay unidades procesadas
            $("#kpiQualityOP").hide();
            $("#titleGeneralCost, #titleUnitCost, #lineDivTitleCost").hide();
          }
        }
      });
    });

    // Opciones de configuración para observar los cambios en el contenido de los elementos
    const config = { childList: true, subtree: true };

    // Iniciar la observación en las columnas específicas
    Object.values(targets).forEach((target) =>
      observer.observe(target, config)
    );
  } else {
    console.error("No se encontraron los elementos para observar.");
  }
});
