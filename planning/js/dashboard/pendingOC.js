$(document).ready(function () {
  setTimeout(() => {
    fetch(`/api/dashboardPendingOC`)
      .then((response) => response.text())
      .then((data) => {
        data = JSON.parse(data);
        GraphPendingOC(data);
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

const GraphPendingOC = (data) => {
  // Separamos los datos por estado para crear los datasets
  const datasets = [];
  data.forEach((item) => {
    datasets.push({
      label: `Estado ${item.status}`,
      data: [item.porcentaje_participacion],
      backgroundColor:
        item.status === 2
          ? "rgba(255, 99, 132, 0.2)"
          : "rgba(75, 192, 192, 0.2)",
      borderColor:
        item.status === 2 ? "rgba(255, 99, 132, 1)" : "rgba(75, 192, 192, 1)",
    });
  });

  const ctx = document.getElementById("pendingOCChart").getContext("2d");

  const myChart = new Chart(ctx, {
    type: "bar",
    data: {
      labels: ["Ejecución Ordenes de Compra"],
      datasets: datasets,
    },
    options: {
      maintainAspectRatio: false, // Para usar el tamaño del contenedor y no mantener el ratio
      scales: {
        y: {
          beginAtZero: true,
          max: 100,
        },
      },
    },
  });
};
