$(document).ready(function () {
  $('#email').prop('disabled', true);

  fetch(`/api/user`)
    .then((response) => response.text())
    .then((data) => {
      data = JSON.parse(data);
      loadProfile(data);
    });

  /* Cargar Perfil de usuario */
  loadProfile = (data) => {
    $('#idUser').val(data.id_user);
    $('#firstname').val(data.firstname);
    $('#lastname').val(data.lastname);
    $('#position').val(data.position);
    $('#email').val(data.email);
    if (data.avatar) avatar.src = data.avatar;
  };

  /* Guardar perfil */
  $('#btnSaveProfile').click(function (e) {
    e.preventDefault();

    firstname = $('#firstname').val();
    lastname = $('#lastname').val();
    sessionStorage.setItem('name', firstname);
    sessionStorage.setItem('lastname', lastname);
    password = $('#password').val();
    conPassword = $('#conPassword').val();

    if (!firstname || firstname == '' || !lastname || lastname == '') {
      toastr.error('Ingrese todos los campos');
      return false;
    }

    if (password != conPassword) {
      toastr.error('Las contraseñas no coinciden');
      return false;
    }

    $('#email').prop('disabled', false);
    let imageProd = $('#formFile')[0].files[0];
    dataProfile = new FormData(formSaveProfile);
    dataProfile.append('avatar', imageProd);

    $.ajax({
      type: 'POST',
      url: '/api/updateProfile',
      data: dataProfile,
      contentType: false,
      cache: false,
      processData: false,
      success: function (resp) {
        message(resp);
      },
    });
  });

  /* Cargar notificación */
  message = (data) => {
    if (data.success == true) {
      avatar = sessionStorage.getItem('avatar');
      firstname = sessionStorage.getItem('name');
      lastname = sessionStorage.getItem('lastname');

      sessionStorage.removeItem('avatar');
      sessionStorage.removeItem('name');
      sessionStorage.removeItem('lastname');

      if (avatar) hAvatar.src = avatar;
      $('.userName').html(`${firstname} ${lastname}`);
      $('#email').prop('disabled', true);

      toastr.success(data.message);
      return false;
    } else if (data.error == true) toastr.error(data.message);
    else if (data.info == true) toastr.info(data.message);
  };
});
