$(document).ready(function () {
  setTimeout(() => {
    fetch(`/api/machinesAvailable`)
      .then((response) => response.text())
      .then((data) => {
        data = JSON.parse(data);
        ChartMachinesAvailable(data);
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

const ChartMachinesAvailable = (data) => {
  // Extraer etiquetas y colores
  const labels = data.map((item) => item.machine);
  const colors = data.map((item) =>
    item.status < 1 ? "rgba(255, 99, 132, 0.2)" : "rgba(54, 162, 235, 0.2)"
  );
  const borderColors = data.map((item) =>
    item.status < 1 ? "rgba(255, 99, 132, 1)" : "rgba(54, 162, 235, 1)"
  );

  // Configurar el gráfico
  const ctx = document
    .getElementById("chartMachinesAvailable")
    .getContext("2d");
  const myChart = new Chart(ctx, {
    type: "bar",
    data: {
      labels: labels,
      datasets: [
        {
          label: "Estado de Máquinas",
          data: Array(data.length).fill(1), // Todas las barras tienen altura 1
          backgroundColor: colors,
          borderColor: borderColors,
          borderWidth: 1,
        },
      ],
    },
    options: {
      maintainAspectRatio: false, // Para usar el tamaño del contenedor y no mantener el ratio
      indexAxis: "y", // Etiquetas en el eje y
      scales: {
        x: {
          min: 0,
          max: 1,
        },
      },
      plugins: {
        legend: {
          display: false,
        },
      },
    },
  });
};