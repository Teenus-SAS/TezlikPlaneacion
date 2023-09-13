$(document).ready(function () {
  getFirstText = (str) => {
    let lastSpace = str.lastIndexOf(' ');

    let lastText = str.slice(0, lastSpace);

    return lastText;
  };

  getLastText = (str) => {
    let lastSpace = str.lastIndexOf(' ');

    let lastText = str.substring(lastSpace + 1);

    return lastText;
  };
});
