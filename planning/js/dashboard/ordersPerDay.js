$(document).ready(function () {
  setTimeout(() => {
    fetch(`/api/dashboardOrdersPerDay`)
      .then((response) => response.text())
      .then((data) => {
        //Parsea los datos
        data = JSON.parse(data);
        ChartOrdersPerDay(data);
      });
  }, 1000);
});

const ChartOrdersPerDay = (data) => {
  // Fechas únicas etiquetas eje X
  const uniqueDays = [...new Set(data.map((item) => item.day))];

  // Agrupar datos por tipo de orden
  const ordersType1 = uniqueDays.map((day) => {
    const order = data.find(
      (item) => item.day === day && item.type_order === 1
    );
    return order ? order.total_orders : 0;
  });

  const ordersType2 = uniqueDays.map((day) => {
    const order = data.find(
      (item) => item.day === day && item.type_order === 2
    );
    return order ? order.total_orders : 0;
  });

  // Calcular la tercera línea con la suma de type_order 1 y 2
  const totalOrders = uniqueDays.map(
    (day, index) => ordersType1[index] + ordersType2[index]
  );

  // Crear el gráfico de líneas con Chart.js
  const ctx = document.getElementById("chartOrdersxDay").getContext("2d");
  const ordersChart = new Chart(ctx, {
    type: "line",
    data: {
      labels: uniqueDays, // Eje X: días únicos
      datasets: [
        {
          label: "Clientes",
          data: ordersType1, // Eje Y: Pedidos tipo 1 por día
          borderColor: "rgba(138, 43, 226, 1)",
          backgroundColor: "rgba(138, 43, 226, 0.2)",
          fill: false,
          tension: 0.1, // Suaviza la curva de la línea
        },
        {
          label: "Internos",
          data: ordersType2, // Eje Y: Pedidos tipo 2 por día
          borderColor: "rgba(255, 205, 86, 1)",
          backgroundColor: "rgba(255, 205, 86, 0.2)",
          fill: false,
          tension: 0.1, // Suaviza la curva de la línea
        },
        {
          label: "Total",
          data: totalOrders, 
          borderColor: "rgba(75, 192, 192, 1)", 
          backgroundColor: "rgba(75, 192, 192, 0.2)", 
          fill: false,
          tension: 0.1,
          borderDash: [5, 5], // Línea punteada para diferenciarla
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
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
            text: "Cantidad de Pedidos",
          },
        },
      },
      plugins: {
        legend: {
          display: true,
          position: "top",
        },
        tooltip: {
          mode: "index",
          intersect: false,
        },
      },
    },
  });
};

// Ejemplo de llamada a la función
/* const days = ["2024-09-25", "2024-09-26"];
const totalOrdersType1 = [3, 7]; // Pedidos para type 1 por día
const totalOrdersType2 = [4, 5]; // Pedidos para type 2 por día
ChartOrdersPerDay(days, totalOrdersType1, totalOrdersType2); */
