$(document).ready(function () {
  setTimeout(() => {
    fetch(`/api/dashboardQuantityOrdersByClients`)
      .then((response) => response.text())
      .then((data) => {
        data = JSON.parse(data);
        GraphQuantityOrdersByClients(data);
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

const GraphQuantityOrdersByClients = (data) => {
  // Preparar los datos para Chart.js
  const chartData = {
    labels: data.map((item) => item.client),
    datasets: [
      {
        label: "Cantidad de Pedidos",
        data: data.map((item) => item.total_pedidos),
        backgroundColor: "rgba(144, 238, 144, 0.2)",
        borderColor: "rgba(144, 238, 144, 1)",
        borderWidth: 1,
      },
    ],
  };

  // Configurar las opciones del gráfico
  const options = {
    maintainAspectRatio: false,
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
      },
    },
  };

  // Crear el gráfico
  const ctx = document.getElementById("chartOrdersClients").getContext("2d");
  const myChart = new Chart(ctx, {
    type: "bar",
    data: chartData,
    options: options,
  });
};