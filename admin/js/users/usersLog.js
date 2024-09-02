$(document).ready(function () {
  /* Cerrar sesión usuarios */

  $(document).on("click", ".closeSession", function (e) {
    e.preventDefault();
    let data = tblCompanies.row($(this).parent()).data();
    let id = data.id_user;

    $.ajax({
      type: "POST",
      url: `/api/closeSessionUser/${id}`,
      contentType: false,
      cache: false,
      processData: false,

      success: function (resp) {
        message(resp);
      },
    });
  });

  /* Mensaje de exito */

  const message = (data) => {
    const { success, error, info, message } = data;
    if (success) {
      updateTable();
      toastr.success(message);
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $("#tblUsersLog").DataTable().clear();
    $("#tblUsersLog").DataTable().ajax.reload();
  }
});
