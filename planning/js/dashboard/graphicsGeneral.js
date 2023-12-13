$(document).ready(function () {
    var chartClasificationABC;
    var anchura = Math.max(
        document.documentElement.clientWidth,
        window.innerWidth || 0
    );

    anchura <= 480 ? (length = 5) : (length = 10);
    
    graphicClassification = (data) => {
        let inventory = [];
        let name = [];
        let value = [];

        inventory.push({ name: 'A', value: data.A }, { name: 'B', value: data.B }, { name: 'C', value: data.C });
        inventory.sort(function (a, b) {
            return b["value"] - a["value"];
        });

        for (let i = 0; i < inventory.length; i++) {
            name.push(inventory[i].name);
            value.push(inventory[i].value);
        }

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
                labels: name,
                // formatter: function (value, context) {
                //     return context.chart.data.labels[context.dataIndex];
                // },
                datasets: [
                    {
                        data: value,
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