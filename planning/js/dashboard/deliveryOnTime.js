$(document).ready(function () {
  setTimeout(() => {
    fetch(`/api/dashboardDeliveredOnTime`)
      .then((response) => response.text())
      .then((data) => {
        data = JSON.parse(data);
        const percentageOnTime = data.deliveredOnTime;

        const ctx = document
          .getElementById("onTimeDeliveryChart")
          .getContext("2d");
        const onTimeDeliveryChart = new Chart(ctx, {
          type: "bar",
          data: {
            labels: [
              "Pedidos Entregados a Tiempo",
              "Pedidos No Entregados a Tiempo",
            ],
            datasets: [
              {
                label: "Porcentaje de Entrega a Tiempo",
                data: [percentageOnTime, 100 - percentageOnTime],
                backgroundColor: [
                  "rgba(75, 192, 192, 0.2)", // Color para "Entregado a tiempo"
                  "rgba(255, 99, 132, 0.2)", // Color para "No entregado a tiempo"
                ],
                borderColor: [
                  "rgba(75, 192, 192, 1)", // Borde para "Entregado a tiempo"
                  "rgba(255, 99, 132, 1)", // Borde para "No entregado a tiempo"
                ],
                borderWidth: 1,
              },
            ],
          },
          options: {
            scales: {
              y: {
                beginAtZero: true,
                max: 100,
              },
            },
          },
        });
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
