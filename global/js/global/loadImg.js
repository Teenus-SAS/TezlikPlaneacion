$(document).ready(function () {
  /* Cargar imagen perfil */
  $('#formFile').change(function (e) {
    e.preventDefault();
    avatar.src = URL.createObjectURL(event.target.files[0]);
  });

  /* Cargar foto compañia */
  $('#formFileC').change(function (e) {
    e.preventDefault();
    logo.src = URL.createObjectURL(event.target.files[0]);
  });
});
