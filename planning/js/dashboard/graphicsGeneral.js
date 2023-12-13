$(document).ready(function () {
    var chartClasificationABC;
    var anchura = Math.max(
        document.documentElement.clientWidth,
        window.innerWidth || 0
    );

    anchura <= 480 ? (length = 5) : (length = 10);
    
    graphicClassification = (data) => { 
        // let maxDataValue = Math.max(...cost);
        // let minDataValue = Math.min(...cost);
        // let valueRange = maxDataValue - minDataValue;

        // let step = Math.ceil(valueRange / 10 / 10) * 10;

        // let maxYValue = Math.ceil(maxDataValue / step) * step + step;

        // isNaN(maxYValue) ? maxYValue = 10 : maxYValue;

        chartClasificationABC ? chartClasificationABC.destroy() : chartClasificationABC;

        const cmc = document.getElementById("chartClasificationABC");
        chartClasificationABC = new Chart(cmc, {
            plugins: [ChartDataLabels],
            type: "bar",
            data: {
                labels: ['A', 'B', 'C'],
                // formatter: function (value, context) {
                //     return context.chart.data.labels[context.dataIndex];
                // },
                datasets: [
                    {
                        data: [data.A, data.B, data.C],
                        backgroundColor: getRandomColor(3),
                        borderWidth: 1,
                    },
                ],
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        // max: maxYValue,
                    },
                    x: {
                        display: false,
                    },
                }, 
                plugins: {
                    legend: {
                        display: false,
                    },
                    datalabels: {
                        anchor: "end",
                        align: 'top',
                        offset: 2,
                        // formatter: (cost) => cost.toLocaleString("es-CO", { maximumFractionDigits: 0 }),
                        color: "black",
                        font: {
                            size: "12",
                            weight: "normal",
                        },
                    },
                },
            },
        });
    };
});