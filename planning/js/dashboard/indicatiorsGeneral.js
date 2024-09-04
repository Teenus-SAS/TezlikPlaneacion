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
