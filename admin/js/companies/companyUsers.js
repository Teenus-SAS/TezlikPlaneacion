$(document).ready(function () {
  /* Cambiar estado de usuarios * empresa */

  $(document).on('click', '.userStatus', function (e) {
    e.preventDefault();
    // Obtener el ID del elemento
    let id = $(this).attr('id');
    // Obtener la parte después del guion '-'
    let id_user = id.split('-')[1]; 

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
    if (success) {
      updateTable();
      toastr.success(message);
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };

  /* Actualizar tabla */

  function updateTable() {
    $('#tblCompanyUsers').DataTable().clear();
    $('#tblCompanyUsers').DataTable().ajax.reload();
  }
});
