$(document).ready(function () {
  setTimeout(() => {
    fetch(`/api/dashboardOrdersPerDay`)
      .then((response) => response.text())
      .then((data) => {
        //Parsea los datos
        data = JSON.parse(data);

        // Extraer los días y las órdenes en dos arreglos
        const days = data.map((order) => order.day);
        const totalOrdersType1 = data.map((order) => order.type_order);
        const totalOrdersType2 = data.map((order) => order.total_orders);

        ChartOrdersPerDay(days, totalOrdersType1, totalOrdersType2);
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

const ChartOrdersPerDay = (days, totalOrdersType1, totalOrdersType2) => {
  // Obtener el valor máximo del conjunto de datos combinados de ambas series
  const maxOrder = Math.max(...totalOrdersType1, ...totalOrdersType2);

  // Crear el gráfico de líneas
  const ctx = document.getElementById("chartOrdersxDay").getContext("2d");
  const ordersChart = new Chart(ctx, {
    type: "line",
    data: {
      labels: days, // Eje X: días
      datasets: [
        {
          label: "Pedidos Clientes", // Línea para el tipo de pedido 1
          data: totalOrdersType1, // Eje Y: total de órdenes para tipo 1
          borderColor: "rgba(75, 192, 192, 1)",
          backgroundColor: "rgba(75, 192, 192, 0.2)",
          fill: true, // Para llenar el área bajo la línea
          tension: 0.1, // Suaviza la curva de la línea
        },
        {
          label: "Pedidos Internos", // Línea para el tipo de pedido 2
          data: totalOrdersType2, // Eje Y: total de órdenes para tipo 2
          borderColor: "rgba(153, 102, 255, 1)",
          backgroundColor: "rgba(153, 102, 255, 0.2)",
          fill: true, // Para llenar el área bajo la línea
          tension: 0.1, // Suaviza la curva de la línea
        },
      ],
    },
    options: {
      maintainAspectRatio: false, // Para usar el tamaño del contenedor y no mantener el ratio
      scales: {
        x: {
          title: {
            display: true,
            text: "Día",
          },
        },
        y: {
          beginAtZero: true,
          max: maxOrder + 1, // Ajusta el eje Y a un punto por encima del dato máximo
          title: {
            display: true,
            text: "Pedidos",
          },
        },
      },
      plugins: {
        legend: {
          display: true, // Mostrar la leyenda para identificar cada serie
        },
        datalabels: {
          display: true, // Habilitar las etiquetas de datos en cada punto
          color: "black",
          anchor: "end", // Anclar la etiqueta al final del punto
          align: "top", // Alinear la etiqueta por encima del punto
          font: {
            size: 12,
          },
          formatter: function (value) {
            // Personalizar el formato de las etiquetas
            return value.toFixed(0); // Mostrar el valor como entero
          },
        },
        tooltip: {
          mode: "index", // Asegurarse de que solo se muestre una tooltip por punto
          intersect: false, // Permite mostrar la tooltip incluso si el cursor no está exactamente sobre el punto
          callbacks: {
            label: function (context) {
              // Personalizar el formato de la tooltip
              return "Pedidos: " + context.parsed.y.toFixed(0);
            },
          },
        },
      },
    },
    plugins: [ChartDataLabels], // Añadir el plugin de etiquetas
  });
};

// Ejemplo de llamada a la función
const days = ["2024-09-25", "2024-09-26"];
const totalOrdersType1 = [3, 7]; // Pedidos para type 1 por día
const totalOrdersType2 = [4, 5]; // Pedidos para type 2 por día
ChartOrdersPerDay(days, totalOrdersType1, totalOrdersType2);
