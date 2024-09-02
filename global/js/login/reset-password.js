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
        if (success) {
          toastr.success(message);
          setTimeout(() => {
            location.href = '../../../';
          }, 2000);
        } else if (error) toastr.error(message);
      },
    });
  });
});
