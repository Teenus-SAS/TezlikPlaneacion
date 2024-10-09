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

  // Variable para mantener referencia del icono actualmente seleccionado
  let selectedIcon = null;

  // Agregar evento click a cada enlace si existe en el DOM
  links.forEach((item) => {
    if (item.link && item.icon) {
      item.link.addEventListener("click", () => {
        // Si hay un icono previamente seleccionado, cambiar su color 
        if (selectedIcon && selectedIcon !== item.icon) {
          selectedIcon.style.color = "gray"; // Restablecer color 
        }

        // Cambiar el color del icono actual 
        item.icon.style.color = "green";

        // Actualizar el icono seleccionado
        selectedIcon = item.icon;
      });
    }
  });
});
