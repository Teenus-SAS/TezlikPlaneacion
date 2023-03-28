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
    if (data.success == true) {
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $("#tblUsersLog").DataTable().clear();
    $("#tblUsersLog").DataTable().ajax.reload();
  }
});
