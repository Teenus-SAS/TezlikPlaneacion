//Ocultar graficos
$(".staffAvailableCardChart. machinesAvailableCardChart").hide();

// Mostrar el gráfico
function showChart(cardClass, chartClass) {
  $(cardClass).click(function (e) {
    e.preventDefault();
    $(chartClass).stop(true, true).slideDown(800);
  });
}

// Asignar la función a los eventos de click
showChart(".staffAvailableCard", ".staffAvailableCardChart");
showChart(".machinesAvailableCard", ".machinesAvailableCardChart");
