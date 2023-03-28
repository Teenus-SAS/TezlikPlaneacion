$(document).ready(function () {
  /* Cambiar estado de usuarios * empresa */

  $(document).on('click', '.userStatus', function (e) {
    e.preventDefault();
    id_user = this.id;

    $.ajax({
      type: 'POST',
      url: `/api/updateCompanyUsersStatus/${id_user}`,
      success: function (resp) {
        message(resp);
      },
    });
  });

  /* Mensaje de exito */

  const message = (data) => {
    if (data.success == true) {
      // $('#createCompany').hide(800);
      // $('#formCreateCompany').trigger('reset')
      updateTable();
      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblCompanyUsers').DataTable().clear();
    $('#tblCompanyUsers').DataTable().ajax.reload();
  }
});
