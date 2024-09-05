$(document).ready(function () {
  setTimeout(() => {
    fetch(`/api/dashboardOrdersPerDay`)
      .then((response) => response.text())
      .then((data) => {
        //Parsea los datos
        data = JSON.parse(data);

        // Extraer los días y las órdenes en dos arreglos
        const days = data.map((order) => order.day);
        const totalOrders = data.map((order) => order.total_orders);

        GraphOrdersPerDay(days, totalOrders);
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

const GraphOrdersPerDay = (days, totalOrders) => {
  // Crear el gráfico de líneas
  const ctx = document.getElementById("ordersChart").getContext("2d");
  const ordersChart = new Chart(ctx, {
    type: "line",
    data: {
      labels: days, // Eje X: días
      datasets: [
        {
          label: "Órdenes por día",
          data: totalOrders, // Eje Y: total de órdenes
          borderColor: "rgba(75, 192, 192, 1)",
          backgroundColor: "rgba(75, 192, 192, 0.2)",
          fill: true, // Para llenar el área bajo la línea
          tension: 0.1, // Suaviza la curva de la línea
        },
      ],
    },
    options: {
      scales: {
        x: {
          title: {
            display: true,
            text: "Día",
          },
        },
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: "Órdenes",
          },
        },
      },
    },
  });
};
