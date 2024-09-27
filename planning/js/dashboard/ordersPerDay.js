$(document).ready(function () {
  setTimeout(() => {
    fetch(`/api/dashboardOrdersPerDay`)
      .then((response) => response.text())
      .then((data) => {
        //Parsea los datos
        data = JSON.parse(data);

        // Extraer los días y las órdenes en dos arreglos
        const days = data.map((order) => order.day);
        const type = data.map((order) => order.type_order);
        const totalOrders = data.map((order) => order.total_orders);

        // Ejemplo de llamada a la función
        /* const days = ["2024-09-25", "2024-09-26"];
        const totalOrdersType1 = [3, 7]; // Pedidos para type 1 por día
        const totalOrdersType2 = [4, 5]; // Pedidos para type 2 por día
        ChartOrdersPerDay(days, totalOrdersType1, totalOrdersType2); */

        ChartOrdersPerDay(days, type, totalOrders);
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

const ChartOrdersPerDay = (days, type, totalOrders) => {
  // Inicializar los datos para las dos líneas
  let cumulativeOrdersType1 = [];
  let cumulativeOrdersType2 = [];

  // Variables para llevar el acumulado de cada tipo
  let totalType1 = 0;
  let totalType2 = 0;

  // Calcular los acumulados por día para cada tipo
  days.forEach((day, index) => {
    if (type[index] === 1) {
      totalType1 += totalOrders[index]; // Acumular las órdenes para type_order 1
    } else if (type[index] === 2) {
      totalType2 += totalOrders[index]; // Acumular las órdenes para type_order 2
    }

    // Añadir el valor acumulado hasta el día actual
    cumulativeOrdersType1.push(totalType1);
    cumulativeOrdersType2.push(totalType2);
  });

  // Obtener el valor máximo del conjunto de datos acumulado
  const maxOrder = Math.max(...cumulativeOrdersType1, ...cumulativeOrdersType2);

  // Crear el gráfico de líneas
  const ctx = document.getElementById("chartOrdersxDay").getContext("2d");
  const ordersChart = new Chart(ctx, {
    type: "line",
    data: {
      labels: days, // Eje X: días
      datasets: [
        {
          label: "Pedidos Tipo 1", // Primera línea para type_order 1
          data: cumulativeOrdersType1, // Eje Y: total acumulado de órdenes type 1
          borderColor: "rgba(75, 192, 192, 1)",
          backgroundColor: "rgba(75, 192, 192, 0.2)",
          fill: false,
          tension: 0.1, // Suaviza la curva de la línea
        },
        {
          label: "Pedidos Tipo 2", // Segunda línea para type_order 2
          data: cumulativeOrdersType2, // Eje Y: total acumulado de órdenes type 2
          borderColor: "rgba(153, 102, 255, 1)",
          backgroundColor: "rgba(153, 102, 255, 0.2)",
          fill: false,
          tension: 0.1, // Suaviza la curva de la línea
        },
      ],
    },
    options: {
      maintainAspectRatio: false, // Para usar el tamaño del contenedor y no mantener el ratio
      scales: {
        x: {
          title: {
            display: false,
            text: "Día",
          },
        },
        y: {
          beginAtZero: true,
          max: maxOrder + 1, // Ajusta el eje Y a un punto por encima del dato máximo
          title: {
            display: false,
            text: "Pedidos",
          },
        },
      },
      plugins: {
        legend: {
          display: true,
          position: 'top',
        },
        datalabels: {
          display: true, // Habilitar las etiquetas
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

