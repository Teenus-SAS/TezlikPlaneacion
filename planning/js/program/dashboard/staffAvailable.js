$(document).ready(function () {
  //Obtener data
  async function fetchStaffAvailable() {
    try {
      const response = await fetch(`/api/staffAvailable`);

      if (!response.ok) throw new Error("Network response was not ok");

      let data = await response.text();
      data = JSON.parse(data);

      await ChartStaffAvailable(data); // Espera a que termine ChartMachinesAvailable
      IndicatorStaffAvailable(data); // Llama a IndicatorMachinesAvailable
    } catch (error) {
      console.error("Error fetching machines available data:", error);
    }
  }

  fetchStaffAvailable();

  const ChartStaffAvailable = (data) => {
    //Obtener labels y valores
    const labels = data.map((item) => `${item.machine}`);
    //const values = data.map((item) => item.total_operadores)

    const availableOperators = data.map((item) => item.operarios_disponibles);
    const unavailableOperators = data.map(
      (item) => item.total_operadores - item.operarios_disponibles
    );

    // Configuración del gráfico
    const ctx = document.getElementById("staffAvailableChart").getContext("2d");
    const myChart = new Chart(ctx, {
      plugins: [ChartDataLabels],
      type: "bar",
      data: {
        labels: labels,
        datasets: [
          {
            label: "Total Disponibles",
            data: availableOperators,
            backgroundColor: "rgba(54, 162, 235, 0.2)",
            borderColor: "rgba(54, 162, 235, 1)",
            borderWidth: 1,
          },
          {
            label: "Operadores No Disponibles",
            data: unavailableOperators,
            backgroundColor: "rgba(255, 99, 132, 0.7)", // Rojo
            borderWidth: 1,
          },
        ],
      },
      options: {
        maintainAspectRatio: false, // Para usar el tamaño del contenedor y no mantener el ratio
        scales: {
          x: {
            stacked: true, // Apilar en el eje X
          },
          y: {
            stacked: true, // Apilar en el eje Y
            beginAtZero: true,
            //max: 35,
            title: {
              display: true,
              text: "Cantidad de Operarios",
            },
          },
        },
        plugins: {
          legend: {
            display: false,
          },
          datalabels: {
            anchor: "center",
            align: "center",
            offset: 2,
            color: "black",
            font: {
              size: "12",
              weight: "normal",
            },
            formatter: function (value) {
              return value > 0 ? value : ""; // Agregar el símbolo de porcentaje a las etiquetas
            },
          },
          tooltip: {
            callbacks: {
              label: function (tooltipItem) {
                return tooltipItem.raw; // Mostrar solo el valor en el tooltip
              },
            },
          },
        },
      },
    });

    IndicatorStaffAvailable = (staff) => {
      let totalStaff = 0;
      let availableStaff = 0;

      for (let i = 0; i < data.length; i++) {
        totalStaff += data[i].total_operadores;
        availableStaff += data[i].operarios_disponibles;
      }

      // Calcular el margen de disponibilidad
      const availableMargin = (availableStaff / totalStaff) * 100;

      $("#staffAvailableIndicator").text(`${availableMargin.toFixed(2)}%`);
    };
  };
});
