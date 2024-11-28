$(document).ready(function () {
  // Función para inicializar el observador
  function initObserver() {
    const targets = {
      defects: document.querySelector(".unitsDefects"),
      processing: document.querySelector(".unitsProcessing"),
      payroll: document.querySelector(".costPayroll"),
      materials: document.querySelector(".costMaterials"),
      indirect: document.querySelector(".costIndirect"),
    };

    // Verifica si todos los elementos existen
    if (Object.values(targets).every((target) => target)) {
      console.log("Elementos encontrados. Iniciando observador...");

      const observer = new MutationObserver(function (mutationsList) {
        mutationsList.forEach((mutation) => {
          if (mutation.type === "childList") {
            console.log("Se detectaron cambios en las columnas observadas.");

            // Funciones auxiliares
            const parseCurrency = (value) =>
              parseFloat(value.replace(/[\$\.]/g, "").replace(",", ".")) || 0;

            const formatCurrency = (value) =>
              `$${value.toLocaleString("es-CO", {
                maximumFractionDigits: 2,
              })}`;

            // Obtener valores actualizados
            const unitsDefects =
              parseFloat($(".unitsDefects").text().replace(" Und", "")) || 0;

            const unitsProcessing =
              parseFloat(
                $(".unitsProcessing")
                  .text()
                  .replace(" Und", "") // Reemplaza " Und"
                  .replace(/\./g, "") // Reemplaza todos los puntos
              ) || 0;

            const costPayroll = parseCurrency($(".costPayroll").text());
            const costMaterials = parseCurrency($(".costMaterials").text());
            const costIndirect = parseCurrency($(".costIndirect").text());

            if (unitsProcessing > 0) {
              // Calcular valores y KPI
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
              $("#kpiCostMaterials").text(
                `MP: ${formatCurrency(costMaterials)}`
              );
              $("#kpiCostMaterialsUnit").text(
                `MP: ${formatCurrency(costMaterialsUnit)}`
              );
              $("#kpiIndirectCost").text(`CI: ${formatCurrency(costIndirect)}`);
              $("#kpiIndirectCostUnit").text(
                `CI: ${formatCurrency(costIndirectUnit)}`
              );
              $("#kpiTotalCost").text(`CT: ${formatCurrency(totalCost)}`);
              $("#kpiTotalCostUnit").text(
                `CT: ${formatCurrency(totalCostUnit)}`
              );

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

      // Configuración del observador
      const config = { childList: true, subtree: true };

      // Iniciar la observación
      Object.values(targets).forEach((target) =>
        observer.observe(target, config)
      );
    } else {
      console.log("Esperando que los elementos estén disponibles...");
      setTimeout(initObserver, 500); // Reintentar después de 500 ms
    }
  }

  // Llamar a la función de inicialización
  initObserver();

  // const parseCurrency = (value) => {
  //   return (
  //     parseFloat(
  //       value
  //         .replace(/\./g, "") // Elimina separadores de miles
  //         .replace(",", ".") // Convierte separador decimal
  //         .replace(/[\$]/g, "") // Elimina el símbolo de moneda
  //     ) || 0
  //   );
  // };

  // const formatCurrency = (value) => {
  //   return `$${value.toLocaleString("es-CO", {
  //     minimumFractionDigits: 2,
  //     maximumFractionDigits: 2,
  //   })}`;
  // };
});
