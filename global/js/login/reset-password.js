$(document).ready(function () {
  $('#btnChangePass').on('click', function (e) {
    let pass = $('#inputNewPass').val();
    let pass1 = $('#inputNewPass1').val();

    let data = $('#frmChangePasword').serialize();

    if (pass != pass1) {
      toastr.error('los password no coinciden intente nuevamente');
      return false;
    }

    $.ajax({
      type: 'POST',
      url: '/api/changePassword',
      data: data,
      success: function (data, textStatus, xhr) {
        if (data.success == true) {
          toastr.success(data.message);
          setTimeout(() => {
            location.href = '../../../';
          }, 2000);
        } else if (data.error == true) toastr.error(data.message);
      },
    });
  });
});
