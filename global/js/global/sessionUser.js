$(document).ready(function () {
  /* Cierre de página */
  $(window).on('mouseover', function () {
    window.onbeforeunload = null;
  });
  $(window).on('mouseout', function () {
    window.onbeforeunload = ConfirmLeave;
  });
  $('body').on('click', 'a', function () {
    window.onbeforeunload = null;
  });

  // Tiempo de inactividad
  (function () {
    var minutes = true;
    var interval = minutes ? 60000 : 1000;
    var IDLE_TIMEOUT = 10;
    var idleCounter = 0;

    document.onmousemove = document.onkeypress = function () {
      idleCounter = 0;
    };

    window.setInterval(function () {
      if (++idleCounter >= IDLE_TIMEOUT) {
        fetchindata();
      }
    }, interval);
  });

  getApi = async (url) => {
    try {
      result = await $.ajax({
        url: url,
      });
      return result;
    } catch (error) {
      return 0;
    }
  };

  // var timeout;
  // var prevKey = '';

  checkSession = async () => {
    data = await getApi('/api/checkSessionUser');

    if (data == 0) {
      location.href = '/';
    }
  };
  checkSession();

  function ConfirmLeave() {
    fetchindata();
  }

  fetchindata = async () => {
    resp = await getApi('/api/logoutInactiveUser');
    if (resp.inactive) {
      location.href = '/';
      toastr.error(resp.message);
    }
  };
});
