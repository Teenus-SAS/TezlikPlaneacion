$(document).ready(function () {
  // Seleccionar todos los enlaces y sus respectivos iconos
  const links = [
    {
      link: document.getElementById("dashboard-link"),
      icon: document.getElementById("dashboard-icon"),
    },
    {
      link: document.getElementById("inventory-link"),
      icon: document.getElementById("inventory-icon"),
    },
    {
      link: document.getElementById("requisitions-link"),
      icon: document.getElementById("requisitions-icon"),
    },
    {
      link: document.getElementById("orders-link"),
      icon: document.getElementById("orders-icon"),
    },
    {
      link: document.getElementById("programming-link"),
      icon: document.getElementById("programming-icon"),
    },
    {
      link: document.getElementById("explosion-link"),
      icon: document.getElementById("explosion-icon"),
    },
    {
      link: document.getElementById("production-order-link"),
      icon: document.getElementById("production-order-icon"),
    },
    {
      link: document.getElementById("store-link"),
      icon: document.getElementById("store-icon"),
    },
    {
      link: document.getElementById("offices-link"),
      icon: document.getElementById("offices-icon"),
    },
  ];

  // Agregar evento click a cada enlace
  links.forEach((item) => {
    item.link.addEventListener("click", () => {
      item.icon.style.color = "green";

      // Cambiar el color de los demás iconos a su color original (gris)
      links.forEach((resetItem) => {
        if (resetItem !== item) {
          resetItem.icon.style.color = "";
        }
      });
    });
  });
});