$(document).ready(function () {
  /* Ocultar card notificacion */
  $(".cardCreateNotification").hide();

  /* Mostrar card notificacion */
  $("#btnNewNotification").click(function (e) {
    e.preventDefault();

    $("#formCreateNotification").trigger("reset");
    sessionStorage.removeItem("id_notification");
    $(".cardCreateNotification").toggle(800);
  });

  /* Agregar notificacion */
  $("#btnCreateNotification").click(function (e) {
    e.preventDefault();

    id_notification = sessionStorage.getItem("id_notification");

    if (id_notification == "" || !id_notification) {
      desc = $("#description").val();
      idCompany = $("#company").val();

      if (desc == "" || !desc || idCompany == "" || !idCompany) {
        toastr.error("Ingrese los campos");
        return false;
      }

      dataNotification = $("#formCreateNotification").serialize();

      $.post(
        "/api/addNotification",
        dataNotification,
        function (data, textStatus, jqXHR) {
          message(data);
        }
      );
    } else updateNotification();
  });

  /* Modificar notificacion */
  $(document).on("click", ".updateNotification", function (e) {
    $(".cardCreateNotification").show(800);
    $("#btnCreateNotification").text("Actualizar");

    const row = $(this).closest("tr")[0];
    let data = tblNotifications.fnGetData(row);

    // Obtener el ID del elemento
    let id_notification = $(this).attr("id").split("-")[1];

    sessionStorage.setItem("id_notification", id_notification);

    $("#description").val(data.description);
    $(`#company option[value=${data.id_company}]`).prop("selected", true);

    $("html, body").animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });

  updateNotification = () => {
    id_notification = sessionStorage.getItem("id_notification");

    dataNotification = $("#formCreateNotification").serialize();

    dataNotification = `${dataNotification}&idNotification=${id_notification}`;

    $.post(
      "/api/updateNotification",
      dataNotification,
      function (data, textStatus, jqXHR) {
        message(data);
      }
    );
  };

  /* Eliminar notificacion */
  deleteFunction = () => {
    const row = $(this.activeElement).closest("tr")[0];

    let data = tblNotifications.fnGetData(row);

    id_notification = data.id_notification;

    bootbox.confirm({
      title: "Eliminar",
      message:
        "Está seguro de eliminar esta notificación? Esta acción no se puede reversar.",
      buttons: {
        confirm: {
          label: "Si",
          className: "btn-success",
        },
        cancel: {
          label: "No",
          className: "btn-danger",
        },
      },
      callback: function (result) {
        if (result) {
          $.get(
            `/api/deleteNotification/${id_notification}`,
            function (data, textStatus, jqXHR) {
              message(data);
            }
          );
        }
      },
    });
  };

  /* Mensaje de exito */
  message = (data) => {
    const { success, error, info, message } = data;
    if (data.success) {
      $(".cardCreateNotification").hide(800);
      $("#formCreateNotification").trigger("reset");
      toastr.success(message);
      updateTable();
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };

  /* Actualizar tabla */
  function updateTable() {
    $("#tblNotifications").DataTable().clear();
    $("#tblNotifications").DataTable().ajax.reload();
  }
});
