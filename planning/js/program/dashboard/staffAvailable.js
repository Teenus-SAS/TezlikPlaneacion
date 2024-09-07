$(document).ready(function () {
  setTimeout(() => {
    fetch(`/api/staffAvailable`)
      .then((response) => response.text())
      .then((data) => {
        data = JSON.parse(data);
        GraphStaffAvailable(data);
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

const GraphStaffAvailable = (data) => {
  // Datos del gráfico (asumiendo que los datos están en una variable llamada 'data')
  const labels = data.map((item) => `${item.machine}`);
  const values = data.map((item) => item.total_operadores);

  // Configuración del gráfico
  const ctx = document.getElementById("staffAvailableChart").getContext("2d");
  const myChart = new Chart(ctx, {
    plugins: [ChartDataLabels],
    type: "bar",
    data: {
      labels: labels,
      datasets: [
        {
          label: "Total Operadores",
          data: values,
          backgroundColor: "rgba(54, 162, 235, 0.2)",
          borderColor: "rgba(54, 162, 235, 1)",
          borderWidth: 1,
        },
      ],
    },
    options: {
      maintainAspectRatio: false, // Para usar el tamaño del contenedor y no mantener el ratio
      scales: {
        y: {
          beginAtZero: true,
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
          formatter: function (value) {
            return value; // Agregar el símbolo de porcentaje a las etiquetas
          },
        },
        tooltip: {
          callbacks: {
            label: function (tooltipItem) {
              return tooltipItem.raw; // Mostrar solo el valor en el tooltip
            },
          },
        },
      },
    },
  });
};
