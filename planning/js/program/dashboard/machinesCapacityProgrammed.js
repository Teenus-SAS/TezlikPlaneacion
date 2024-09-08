$(document).ready(function () {
  setTimeout(() => {
    fetch(`/api/machinesCapacityProgrammed`)
      .then((response) => response.text())
      .then((data) => {
        data = JSON.parse(data);
        ChartMachinesCapacityProgrammed(data);
      });
  }, 3000);

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

const ChartMachinesCapacityProgrammed = (data) => {
  //Obtener labels y valores
  const labels = data.map((item) => `${item.machine}`);
  const values = data.map((item) => item.total_minutes);

  //Graficar
  const ctx = document.getElementById("machineMinutesChart").getContext("2d");
  const machineMinutesChart = new Chart(ctx, {
    type: "bar",
    data: {
      labels: labels, // Nombres de las máquinas
      datasets: [
        {
          label: "Total Minutes",
          data: values, // Minutos por máquina
          backgroundColor: [
            "rgba(75, 192, 192, 0.2)",
            "rgba(153, 102, 255, 0.2)",
            "rgba(255, 159, 64, 0.2)",
            "rgba(54, 162, 235, 0.2)",
          ],
          borderColor: [
            "rgba(75, 192, 192, 1)",
            "rgba(153, 102, 255, 1)",
            "rgba(255, 159, 64, 1)",
            "rgba(54, 162, 235, 1)",
          ],
          borderWidth: 1,
        },
      ],
    },
    options: {
      scales: {
        y: {
          beginAtZero: true,
        },
      },
      plugins: {
        legend: {
          display: false, // Oculta la leyenda
        },
        datalabels: {
          anchor: "center",
          align: "center",
          color: "black",
          font: {
            size: 12,
            weight: "bold",
          },
          formatter: function (value) {
            return value; // Mostrar el valor en el centro de la barra
          },
        },
      },
    },
  });
};
