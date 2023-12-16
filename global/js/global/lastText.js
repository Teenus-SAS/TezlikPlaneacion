$(document).ready(function () {
  getFirstText = (str) => {
    let firstSpace = str.indexOf(' ');

    let firstText = str.substring(0, firstSpace);

    return firstText;
  };

  getLastText = (str) => {
    let lastSpace = str.lastIndexOf(' ');

    let lastText = str.substring(lastSpace + 1);

    return lastText;
  };
});
