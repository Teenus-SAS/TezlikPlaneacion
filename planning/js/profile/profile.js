$(document).ready(function () {
  $("#email").prop("disabled", true);

  /* Cargar Perfil de usuario */
  loadProfile = async () => {
    let data = await searchData("/api/user");

    $("#profileName").html(data.firstname);
    $("#idUser").val(data.id_user);
    $("#firstname").val(data.firstname);
    $("#lastname").val(data.lastname);
    $("#position").val(data.position);
    $("#email").val(data.email);
    if (data.avatar) avatar.src = data.avatar;

    /* Cargar data compañia */
    data = await searchData("/api/company");

    $("#idCompany").val(data[0].id_company);
    $("#state").val(data[0].state);
    $("#company").val(data[0].company);
    $("#nit").val(data[0].nit);
    $("#city").val(data[0].city);
    $("#country").val(data[0].country);
    $("#phone").val(data[0].telephone);
    $("#address").val(data[0].address);
    if (data[0].logo) $("#logo").prop("src", data[0].logo);
  };

  loadProfile();

  /* Guardar perfil */
  $("#btnSaveProfile").click(function (e) {
    e.preventDefault();

    firstname = $("#firstname").val();
    lastname = $("#lastname").val();
    sessionStorage.setItem("name", firstname);
    sessionStorage.setItem("lastname", lastname);
    password = $("#password").val();
    conPassword = $("#conPassword").val();

    if (!firstname || firstname == "" || !lastname || lastname == "") {
      toastr.error("Ingrese todos los campos");
      return false;
    }

    if (password != conPassword) {
      toastr.error("Las contraseñas no coinciden");
      return false;
    }

    $("#email").prop("disabled", false);
    let imageProd = $("#formFile")[0].files[0];
    let imageCompany = $('#formFileC')[0].files[0];
    dataProfile = new FormData(formSaveProfile);
    dataProfile.append("avatar", imageProd);
    dataProfile.append('logo', imageCompany);
    dataProfile.append("admin", 0);

    $.ajax({
      type: "POST",
      url: "/api/updateProfile",
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
    const { success, error, info, message } = data;
    if (success) {
      // avatar = sessionStorage.getItem('avatar');
      firstname = sessionStorage.getItem("name");
      lastname = sessionStorage.getItem("lastname");

      // sessionStorage.removeItem('avatar');
      sessionStorage.removeItem("name");
      sessionStorage.removeItem("lastname");

      // if (avatar) hAvatar.src = avatar;
      $(".userName").html(`${firstname} ${lastname}`);
      $("#email").prop("disabled", true);

      toastr.success(message);
      return false;
    } else if (error) toastr.error(message);
    else if (info) toastr.info(message);
  };
});
