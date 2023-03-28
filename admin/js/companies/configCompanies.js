$(document).ready(function () {
  $.ajax({
    url: '/api/allCompanies',
    success: function (r) {
      let $select = $(`#company`);
      $select.empty();

      $select.append(`<option disabled selected>Seleccionar</option>`);
      $.each(r, function (i, value) {
        $select.append(
          `<option value = ${value.id_company}> ${value.company} </option>`
        );
      });
    },
  });
});
