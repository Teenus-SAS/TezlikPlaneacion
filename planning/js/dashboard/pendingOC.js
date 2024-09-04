$(document).ready(function () {
  setTimeout(() => {
    fetch(`/api/dashboardPendingOC`)
      .then((response) => response.text())
      .then((data) => {
        data = JSON.parse(data);
        GraphPendingOC(data.executed_requisitions);
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

const GraphPendingOC = (executed_requisitions) => {
  const ctx = document.getElementById("pendingOCChart").getContext("2d");
  const onTimeDeliveryChart = new Chart(ctx, {
    type: "bar",
    data: {
      labels: ["Pedidos"],
      datasets: [
        {
          label: "",
          data: [pendingOC],
          backgroundColor: "rgba(75, 192, 192, 0.2)", // Color para "Entregado a tiempo"
          borderColor: "rgba(75, 192, 192, 1)", // Borde para "Entregado a tiempo"
          borderWidth: 1,
        },
        /* {
          label: "",
          data: [100 - pendingOC],
          backgroundColor: "rgba(255, 99, 132, 0.2)", // Color para "No entregado a tiempo"
          borderColor: "rgba(255, 99, 132, 1)", // Borde para "No entregado a tiempo"
          borderWidth: 1,
        }, */
      ],
    },
    options: {
      scales: {
        x: {
          stacked: true, // Apilamiento en el eje X
          display: false,
        },
        y: {
          stacked: true, // Apilamiento en el eje Y
          beginAtZero: true,
          max: 100,
          ticks: {
            display: false,
          },
        },
      },
      plugins: {
        legend: {
          display: false,
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
