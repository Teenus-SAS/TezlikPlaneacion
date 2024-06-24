$(document).ready(function () {
  // Variable para almacenar la última ruta visitada
  let lastVisitedRoute = window.location.pathname;

  // Tiempo de inactividad
  (function () {
    var minutes = true;
    var interval = minutes ? 60000 : 1000;
    var IDLE_TIMEOUT = 10;
    var idleCounter = 0;

    // Reiniciar contador de inactividad en cada interacción del usuario
    function resetIdleCounter() {
      idleCounter = 0;
    }

    document.addEventListener('mousemove', resetIdleCounter);
    document.addEventListener('keypress', resetIdleCounter);

    window.setInterval(function () {
      if (++idleCounter >= IDLE_TIMEOUT) {
        fetchindata();
      }
    }, interval);
  })();

  // Función para realizar solicitudes AJAX
  async function getApi(url) {
    try { 
      const response = await fetch(url);
      if (response.ok) {
        return await response.json();
      } else {
        return 0;
      }
    } catch (error) {
      // console.error('Error fetching data:', error);
      return 0;
    }
  }

  // Función para verificar la sesión del usuario
  async function checkSession() {
    const data = await getApi('/api/checkSessionUser'); 
    // debugger
    if (data === 0) {
      location.href = '/';
    }
  }
  checkSession();

  // Función para desloguear al usuario
  async function fetchindata() {
    // Verifica si el usuario sigue en la misma ruta
    if (window.location.pathname === lastVisitedRoute) {
      // Solo ejecuta el logout si sigue en la misma ruta
      const resp = await getApi('/api/logoutInactiveUser');
 
      if (resp && resp.inactive) {
        location.href = '/';
        toastr.error(resp.message);
      }
    }
  }

  // Manejar el evento beforeunload para asegurar el logout al cerrar la pestaña
  window.addEventListener('beforeunload', async function (e) {
    // Ejecutar fetchindata antes de cerrar la pestaña
    await fetchindata();
  });

  // Actualiza la última ruta visitada cuando cambia la URL
  window.addEventListener('popstate', function() {
    lastVisitedRoute = window.location.pathname;
  });

  // Actualiza la última ruta visitada cuando se hace clic en un enlace
  $(document).on('click', 'a', function() {
    lastVisitedRoute = $(this).attr('href');
  });
});