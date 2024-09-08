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
chartMachineCapacityProgrammed;
const ChartMachinesCapacityProgrammed = (data) => {
  //Obtener labels y valores
  const machineNames = data.map((item) => `${item.machine_name}`);
  const capacityHours = data.map((item) => item.monthly_capacity_hours);
  const programmedHours = data.map((item) => item.total_programmed_hours);

  //Graficar
  // Crear el gráfico de barras apiladas
  const ctx = document.getElementById("chartMachineCapacityProgrammed").getContext("2d");
  const capacityChart = new Chart(ctx, {
    type: "bar",
    data: {
      labels: machineNames, // Nombres de las máquinas en el eje X
      datasets: [
        {
          label: "Capacidad Total Mensual (Horas)", // Etiqueta para la capacidad total mensual
          data: capacityHours, // Datos de capacidad total mensual
          backgroundColor: "rgba(75, 192, 192, 0.8)", // Azul para la capacidad total
          borderColor: "rgba(75, 192, 192, 1)",
          borderWidth: 1,
        },
        {
          label: "Capacidad Programada (Horas)", // Etiqueta para la capacidad programada
          data: programmedHours, // Datos de capacidad programada
          backgroundColor: "rgba(255, 205, 86, 0.8)", // Amarillo para la capacidad programada
          borderColor: "rgba(255, 205, 86, 1)",
          borderWidth: 1,
        },
      ],
    },
    options: {
      scales: {
        x: {
          stacked: true, // Apilamiento en el eje X
          title: {
            display: true,
            text: "Máquinas",
          },
        },
        y: {
          stacked: true, // Apilamiento en el eje Y
          beginAtZero: true,
          title: {
            display: true,
            text: "Horas",
          },
        },
      },
      plugins: {
        legend: {
          display: true, // Mostrar la leyenda
          position: "top", // Colocar la leyenda arriba
        },
        tooltip: {
          callbacks: {
            label: function (tooltipItem) {
              return tooltipItem.raw + " horas"; // Mostrar el valor en horas en el tooltip
            },
          },
        },
      },
    },
  });
};
