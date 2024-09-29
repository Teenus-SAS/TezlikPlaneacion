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
  // Datos proporcionados
  const data = [
    { day: "2024-09-26", type_order: 1, total_orders: 1 },
    { day: "2024-09-26", type_order: 2, total_orders: 3 },
    { day: "2024-09-27", type_order: 1, total_orders: 1 },
    { day: "2024-09-28", type_order: 1, total_orders: 1 },
    { day: "2024-09-28", type_order: 2, total_orders: 2 },
  ];

  // Obtener las fechas únicas para las etiquetas del eje X
  const uniqueDays = [...new Set(data.map((item) => item.day))];

  // Agrupar los datos por type_order 1 y 2 para las dos líneas
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

  // Crear el gráfico de líneas con Chart.js
  const ctx = document.getElementById("ordersChart").getContext("2d");
  const ordersChart = new Chart(ctx, {
    type: "line",
    data: {
      labels: uniqueDays, // Eje X: días únicos
      datasets: [
        {
          label: "Pedidos Tipo 1",
          data: ordersType1, // Eje Y: Pedidos tipo 1 por día
          borderColor: "rgba(75, 192, 192, 1)",
          backgroundColor: "rgba(75, 192, 192, 0.2)",
          fill: false,
          tension: 0.1, // Suaviza la curva de la línea
        },
        {
          label: "Pedidos Tipo 2",
          data: ordersType2, // Eje Y: Pedidos tipo 2 por día
          borderColor: "rgba(255, 99, 132, 1)",
          backgroundColor: "rgba(255, 99, 132, 0.2)",
          fill: false,
          tension: 0.1, // Suaviza la curva de la línea
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
