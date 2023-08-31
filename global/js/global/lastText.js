$(document).ready(function () {
  getLastText = (str) => {
    let lastSpace = str.lastIndexOf(' ');

    let lastText = str.substring(lastSpace + 1);

    return lastText;
  };
});
