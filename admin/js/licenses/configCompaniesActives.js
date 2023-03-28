$(document).ready(function () {
  $.ajax({
    url: '/api/companies/1',
    success: function (r) {
      let $select = $(`#company`);
      $select.empty();

      $select.append(`<option disabled selected>Seleccionar</option>`);
      $select.append(`<option value=0>Todas</option>`);
      $.each(r, function (i, value) {
        $select.append(
          `<option value = ${value.id_company}> ${value.company} </option>`
        );
      });
    },
  });
});
