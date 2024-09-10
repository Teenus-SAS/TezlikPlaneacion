//Ocultar graficos
$(".staffAvailableCardChart. machinesAvailableCardChart").hide();

// Función para mostrar el gráfico correspondiente
function showChart(cardClass, chartClass) {
  $(cardClass).click(function (e) {
    e.preventDefault();
    $(chartClass).stop(true, true).slideDown(800); // Usar slideDown para una animación más suave
  });
}

// Asignar la función a los eventos de click
showChart(".staffAvailableCard", ".staffAvailableCardChart");
showChart(".machinesAvailableCard", ".machinesAvailableCardChart");
