$(document).ready(function () {
  setTimeout(() => {
    fetch(`/api/dashboardDeliveredOnTime`)
      .then((response) => response.text())
      .then((data) => {
        data = JSON.parse(data);
        GraphDeliverOnTime(data.deliveredOnTime, data.totalDeliveredOnTime);
      });
  }, 1000);

  /* Colors */
  dynamicColors = () => {
    let letters = "0123456789ABCDEF".split("");
    let color = "#";

    for (var i = 0; i < 6; i++)
      color += letters[Math.floor(Math.random() * 16)];
    return color;
  };

  getRandomColor = (a) => {
    let color = [];
    for (i = 0; i < a; i++) color.push(dynamicColors());
    return color;
  };
});

const GraphDeliverOnTime = (percentageOnTime, totalOrders) => {
  const ctx = document.getElementById("onTimeDeliveryChart").getContext("2d");
  const onTimeDeliveryChart = new Chart(ctx, {
    plugins: [ChartDataLabels],
    type: "bar",
    data: {
      labels: ["Pedidos"],
      datasets: [
        {
          label: "",
          data: [percentageOnTime],
          backgroundColor: "rgba(75, 192, 192, 0.2)", // Color para "Entregado a tiempo"
          borderColor: "rgba(75, 192, 192, 1)", // Borde para "Entregado a tiempo"
          borderWidth: 1,
        },
        {
          label: "",
          data: [100 - percentageOnTime],
          backgroundColor: "rgba(255, 99, 132, 0.2)", // Color para "No entregado a tiempo"
          borderColor: "rgba(255, 99, 132, 1)", // Borde para "No entregado a tiempo"
          borderWidth: 1,
        },
      ],
    },
    options: {
      maintainAspectRatio: false, // Para usar el tamaño del contenedor y no mantener el ratio
      scales: {
        x: {
          stacked: true, // Apilamiento en el eje X
          title: {
            display: false,
            text: "Pedidos", // Etiqueta para el eje X
          },
        },
        y: {
          stacked: true, // Apilamiento en el eje Y
          beginAtZero: true,
          max: 100,
          title: {
            display: true,
            text: "", // Etiqueta para el eje Y
          },
          ticks: {
            callback: function (value) {
              !value ? value = 0 : value;

              return value.toLocaleString('es-CO',{minimumFractionDigits:2,maximumFractionDigits:2}) + "%"; // Agregar el símbolo de porcentaje en el eje Y
            },
          },
        },
      },
      plugins: {
        legend: {
          display: false,
        },
        datalabels: {
          anchor: "center",
          align: "center",
          offset: 2,
          color: "black",
          font: {
            size: "12",
            weight: "normal",
          },
          formatter: function (value, context) {
            !value ? value = 0 : value;

            if (context.datasetIndex === 0) {
              // Calcula el número de pedidos entregados a tiempo
              const onTimeOrders = Math.round(
                (percentageOnTime / 100) * totalOrders
              );
              return `${onTimeOrders} (${value.toLocaleString('es-CO',{minimumFractionDigits:2,maximumFractionDigits:2})}%)`; // Muestra el número y el porcentaje
            } else {
              // Calcula el número de pedidos no entregados a tiempo
              const notOnTimeOrders =
                totalOrders -
                Math.round((percentageOnTime / 100) * totalOrders);
              return `${notOnTimeOrders} (${value.toLocaleString('es-CO',{minimumFractionDigits:2,maximumFractionDigits:2})}%)`; // Muestra el número y el porcentaje
            }
          },
        },
        tooltip: {
          callbacks: {
            label: function (tooltipItem) {
              return tooltipItem.raw + "%"; // Mostrar solo el valor en el tooltip
            },
          },
        },
      },
    },
  });
};
