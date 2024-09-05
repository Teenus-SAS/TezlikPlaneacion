$(document).ready(function () {
  setTimeout(() => {
    fetch(`/api/dashboardGeneral`)
      .then((response) => response.text())
      .then((data) => {
        data = JSON.parse(data);
        graphicClassification(data.classification);
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

graphicClassification = (data) => {
  // Calcular el total y los porcentajes
  const total = Object.values(data).reduce((acc, curr) => acc + curr, 0);
  const dataWithPercentages = Object.entries(data).map(([label, value]) => ({
    label,
    value,
    percentage: ((value / total) * 100).toFixed(2),
  }));

  // Colores aleatorios para cada barra (puedes personalizarlos)
  const colors = [
    "rgba(54, 162, 235, 0.2)",
    "rgba(255, 206, 86, 0.2)",
    "rgba(255, 99, 132, 0.2)",
  ];

  const colorsBackground = [
    "rgba(54, 162, 235, 1)",
    "rgba(255, 206, 86, 1)",
    "rgba(255, 99, 132, 1)",
  ];

  // Configurar los datos para Chart.js
  const chartData = {
    labels: dataWithPercentages.map((item) => item.label),
    datasets: [
      {
        label: "Valores",
        data: dataWithPercentages.map((item) => item.value),
        backgroundColor: colors,
        borderColor: colorsBackground,
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
      elements: {
        bar: {
          borderWidth: 4, // Aumenta el grosor del borde
        },
      },
      tooltip: {
        callbacks: {
          label: (context) => {
            const label = context.label;
            const value = context.parsed.y;
            const percentage = dataWithPercentages.find(
              (item) => item.label === label
            ).percentage;
            return `${label}: ${value} (${percentage}%)`;
          },
        },
      },
      datalabels: {
        anchor: "center",
        align: "center",
        color: "black",
        font: {
          weight: "bold",
        },
        formatter: (value, context) => {
          const dataset = context.dataset;
          const index = context.dataIndex;
          const label = dataset.label;
          const dataPoint = dataset.data[index];
          const percentage = dataWithPercentages.find(
            (item) => item.label === label
          ).percentage;
          return `${value} (${percentage}%)`;
        },
      },
    },
  };

  // Crear el gráfico
  const ctx = document.getElementById("chartClasificationABC").getContext("2d");
  const myChart = new Chart(ctx, {
    type: "bar",
    data: chartData,
    options: options,
  });
};
