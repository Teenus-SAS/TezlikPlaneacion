$(document).ready(function () {
  forgotPass = () => {
    email = $('#email').val();

    if (!email || email == '') {
      toastr.error('Ingrese email');
      return false;
    }

    $.ajax({
      type: 'POST',
      url: '/api/forgotPassword',
      data: { data: email },
      success: function (data, textStatus, xhr) {
        const { success, error, info, message } = data;
        if (success) {
          toastr.success(message);
          setTimeout(() => {
            location.href = '../../../';
          }, 4000);
        } else if (info) toastr.info(message);
        else if (error) toastr.error(message);
      },
    });
  };
});
